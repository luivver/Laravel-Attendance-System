<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Employee;
use Carbon\Carbon;
// use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $this->refresh();

        $files = Storage::files('attendances');
        $fileNames = array_map(function ($file) {
            return basename($file);
        }, $files);

        $attendancesQuery = Attendance::with('employee');

        if ($request->has('year') && !empty($request->input('year'))) {
            $attendancesQuery->whereYear('date', $request->input('year'));
        }

        if ($request->has('month') && !empty($request->input('month'))) {
            $attendancesQuery->whereMonth('date', $request->input('month'));
        }

        if ($request->has('date') && !empty($request->input('date'))) {
            $attendancesQuery->whereDay('date', $request->input('date'));
        }

        // Count late and quota not met before pagination
        $lateCount = (clone $attendancesQuery)->where('late_seconds', '>', 0)->count();
        $quotaCount = (clone $attendancesQuery)->where('work_dur', '<', 9 * 3600)->count();


        $attendances = $attendancesQuery->paginate(40);

        return view('attendance.index', compact('fileNames', 'attendances', 'lateCount', 'quotaCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $fileName = 'absen_' . now()->format('Ymd_His') . '.' . $file->getClientOriginalExtension();
        $file->storeAs('attendances', $fileName);
        $this->processFile($file, $fileName);

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties(['file_name' => $fileName])
            ->log('Admin mengimpor file ' . $fileName);

        return redirect()->route('attendance.index')->with('success', 'File berhasil di-import!');
    }

    private function processFile($file, $fileName)
    {
        $contents = file_get_contents($file);
        // dd($contents);

        $lines = preg_split('/\r\n|\r|\n/', $contents);
        // dd($lines);

        // Skip the header row
        array_shift($lines);

        // Menentukan delimiter berdasarkan ekstensi file
        $extension = $file->getClientOriginalExtension();
        $delimiter = ($extension === 'txt') ? "\t" : ",";

        $attendanceRecords = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $data = str_getcsv($line, $delimiter);
            // dd($data);

            if (count($data) >= 3) {
                $dateStr = trim($data[0]);
                $hms = trim($data[1]);
                $employeeNumber = trim($data[2]);

                // Konversi tanggal dan waktu
                $date = Carbon::createFromFormat('d/m/Y H:i:s', "$dateStr $hms");
                // dd($date);
                if (!$date) {
                    continue; // Skip jika format tanggal atau waktu tidak valid
                }

                $dateKey = $date->format('Y-m-d');
                $time = $date->format('H:i:s');

                // Cek apakah sudah ada data untuk karyawan dan tanggal ini
                if (!isset($attendanceRecords[$employeeNumber][$dateKey])) {
                    $attendanceRecords[$employeeNumber][$dateKey] = [
                        'check_in' => $time,
                        'check_out' => $time,
                    ];
                } else {
                    // Update check_in dan check_out berdasarkan waktu
                    if ($time < $attendanceRecords[$employeeNumber][$dateKey]['check_in']) {
                        $attendanceRecords[$employeeNumber][$dateKey]['check_in'] = $time;
                    }
                    if ($time > $attendanceRecords[$employeeNumber][$dateKey]['check_out']) {
                        $attendanceRecords[$employeeNumber][$dateKey]['check_out'] = $time;
                    }
                }
            }
        }

        // Simpan data ke database
        foreach ($attendanceRecords as $employeeNumber => $dates) {
            foreach ($dates as $date => $times) {
                // Ambil shift dari model Schedule
                $schedule = Schedule::where('num_sch', $employeeNumber)
                    ->whereDate('date', $date)
                    ->first();

                if (!$schedule || !$schedule->shift_start) {
                    Log::warning("Schedule or shift start not found for employee $employeeNumber on $date");
                    $lateSeconds = 0;
                } else {
                    $shiftStart = Carbon::createFromFormat('H:i:s', $schedule->shift_start, 'UTC')->setDateFrom($date);
                    $shiftEnd = Carbon::createFromFormat('H:i:s', $schedule->shift_end, 'UTC')->setDateFrom($date);

                    // Calculate Check-in and Check-out times
                    $checkInDateTime = Carbon::createFromFormat('H:i:s', $times['check_in'], 'UTC')->setDateFrom($date);
                    $checkOutDateTime = Carbon::createFromFormat('H:i:s', $times['check_out'], 'UTC')->setDateFrom($date);

                    $lateSeconds = 0;

                    if ($checkInDateTime > $shiftStart) {
                        $lateSeconds = $checkInDateTime->diffInSeconds($shiftStart);
                    }

                    // Jika check_in < shiftStart maka pakai shiftStart utk hitung waktu mulai
                    if ($checkInDateTime < $shiftStart) {
                        $checkInDateTime = clone $shiftStart;
                    }

                    $shiftEndPlus30 = $shiftEnd->copy()->addMinutes(30);

                    // Adjust checkOutDateTime if it exceeds shiftEndPlus30
                    if ($checkOutDateTime > $shiftEndPlus30) {
                        $checkOutDateTime = $shiftEndPlus30;
                    }

                    // max work Dur yaitu 9 jam
                    $workingDuration = min($checkOutDateTime->diffInSeconds($checkInDateTime), 34600);

                    // dd($employeeNumber);
                    // Simpan ke database
                    Attendance::create([
                        'file_name' => $fileName,
                        'date' => $date,
                        'num_atd' => $employeeNumber,
                        'late_seconds' => $lateSeconds,
                        'check_in' => $times['check_in'],
                        'check_out' => $times['check_out'],
                        'work_dur' => $workingDuration,
                    ]);

                    // dd('Data saved successfully for employee ' . $employeeNumber);
                }
            }
        }
    }

    public function show(Request $request, $fileName)
    {
        // Initialize query for filtering
        $attendancesQuery = Attendance::where('file_name', $fileName);

        // Apply department filter if provided
        $departmentFilter = $request->input('department');
        if ($departmentFilter) {
            $attendancesQuery->whereHas('employee', function ($query) use ($departmentFilter) {
                $query->where('department', $departmentFilter);
            });
        }

        // Apply date filter if provided
        $dateFilter = $request->input('filter_tgl_shift');
        if ($dateFilter) {
            $attendancesQuery->whereDate('shift_date', $dateFilter);
        }

        // Count late and quota not met before pagination
        $lateCount = (clone $attendancesQuery)->where('late_seconds', '>', 0)->count();
        $quotaCount = (clone $attendancesQuery)->where('work_dur', '<', 9 * 3600)->count();


        $attendances = $attendancesQuery->paginate(40);

        // Get distinct departments for filter dropdown
        $departments = Employee::select('department')->distinct()->pluck('department');

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties(['file_name' => $fileName, 'department_filter' => $departmentFilter, 'date_filter' => $dateFilter])
            ->log('Admin membuka file ' . $fileName);

        // Pass data to the view
        return view('attendance.show', compact('attendances', 'fileName', 'lateCount', 'quotaCount', 'departments', 'departmentFilter', 'dateFilter'));
    }

    public function destroy($fileName)
    {
        Attendance::where('file_name', $fileName)->delete();
        Storage::delete('attendances/' . $fileName);

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties([
                'file_name' => $fileName,
            ])
            ->log('Admin menghapus file ' . $fileName);

        return redirect()->route('attendance.index')->with('success', 'File dan isi file berhasil dihapus!');
    }

    public function downloadRecordsCsv(Request $request)
    {
        $query = Attendance::latest();

        // Filter berdasarkan tahun
        if ($request->has('year') && !empty($request->input('year'))) {
            $query->whereYear('date', $request->input('year'));
        }

        // Filter berdasarkan bulan
        if ($request->has('month') && !empty($request->input('month'))) {
            $query->whereMonth('date', $request->input('month'));
        }

        // Filter berdasarkan tanggal
        if ($request->has('date') && !empty($request->input('date'))) {
            $query->whereDay('date', $request->input('date'));
        }

        $data = $query->get();

        // Penamaan file berdasarkan filter
        $filename = "rekap_absensi";
        if ($request->input('year')) {
            $filename .= '_tahun_' . $request->input('year');
        }
        if ($request->input('month')) {
            $filename .= '_bulan_' . $request->input('month');
        }
        if ($request->input('date')) {
            $filename .= '_tanggal_' . $request->input('date');
        }
        $filename .= ".csv";

        // Create a temporary file
        // $tempFile = tempnam(sys_get_temp_dir(), $filename);

        $fp = fopen($filename, 'w+');

        // Menulis header CSV
        fputcsv($fp, [
            'Tanggal',
            'Nomor Karyawan',
            'Tap-In',
            'Tap-Out',
            'Status Kehadiran',
            'Pot Terlambat',
            'Status Kuota Harian',
            'Pot Kuota Harian'
        ]);

        // Menulis data ke dalam CSV
        foreach ($data as $row) {
            $lateSeconds = $row->late_seconds;
            $hours = floor($lateSeconds / 3600);
            $minutes = floor(($lateSeconds % 3600) / 60);
            $seconds = $lateSeconds % 60;
            $formattedLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $workTime = $row->work_dur;
            $whours = floor($workTime / 3600);
            $wminutes = floor(($workTime % 3600) / 60);
            $wseconds = $workTime % 60;
            $formattedWorkTime = sprintf('%02d:%02d:%02d', $whours, $wminutes, $wseconds);

            fputcsv($fp, [
                $row->date,
                $row->num_atd,
                $row->check_in,
                $row->check_out,
                ($lateSeconds > 0) ? "Terlambat" : "Tepat Waktu",
                $formattedLate,
                ($workTime < 32400) ? "Kuota Tidak terpenuhi" : "Kuota Terpenuhi",
                $formattedWorkTime
            ]);
        }

        fclose($fp);

        $headers = [
            'Content-Type' => 'text/csv',
        ];

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties(['filters' => $request->all(), 'filename' => $filename])
            ->log('Admin mengunduh ' . $filename);

        return response()->download($filename, $filename, $headers);
    }


    public function downloadFileRecordsCsv($fileName)
    {
        $data = Attendance::where('file_name', $fileName)->get();
        $filename = "rekap_absensi_{$fileName}.csv";
        $fp = fopen($filename, 'w+');

        fputcsv($fp, array(
            'Tanggal',
            'Nomor Karyawan',
            'Tap-In',
            'Tap-Out',
            'Status Kehadiran',
            'Pot Terlambat',
            'Status Kuota Harian',
            'Pot Kuota Harian',
        ));

        foreach ($data as $row) {
            $lateSeconds = $row->late_seconds;
            $hours = floor($lateSeconds / 3600);
            $minutes = floor(($lateSeconds % 3600) / 60);
            $seconds = $lateSeconds % 60;
            $formattedLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $workTime = $row->work_dur;
            $whours = floor($workTime / 3600);
            $wminutes = floor(($workTime % 3600) / 60);
            $wseconds = $workTime % 60;
            $formattedWorkTime = sprintf('%02d:%02d:%02d', $whours, $wminutes, $wseconds);

            fputcsv($fp, [
                $row->date,
                $row->num_atd,
                $row->check_in,
                $row->check_out,
                ($lateSeconds > 0) ? "Terlambat" : "Tepat Waktu",
                $formattedLate,
                ($workTime < 32400) ? "Kuota Tidak terpenuhi" : "Kuota Terpenuhi",
                $formattedWorkTime,
            ]);
        }

        fclose($fp);

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties(['filename' => $filename])
            ->log('Admin mengunduh ' . $filename);

        return response()->download($filename, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function refresh()
    {
        // Fetch schedules with related employee data
        $schedules = Schedule::with('employee')->get(); // Corrected relationship name

        foreach ($schedules as $schedule) {
            $attendance = Attendance::where('num_atd', $schedule->num_sch)
                ->where('date', $schedule->date)
                ->first();

            if ($attendance) {
                try {
                    if (!$schedule->shift_start || !$schedule->shift_end) {
                        Log::warning("Shift start or shift end not found for employee {$schedule->num_sch} on date {$schedule->date}");
                        continue;
                    }

                    $date = Carbon::parse($schedule->date);
                    $shiftStart = Carbon::createFromFormat('H:i:s', $schedule->shift_start)->setDateFrom($date);
                    $shiftEnd = Carbon::createFromFormat('H:i:s', $schedule->shift_end)->setDateFrom($date);
                    $shiftEndPlus30 = $shiftEnd->copy()->addMinutes(30);

                    $checkInTime = $attendance->check_in;
                    $checkOutTime = $attendance->check_out;

                    $checkInDateTime = Carbon::createFromFormat('H:i:s', $checkInTime)->setDateFrom($date);
                    $checkOutDateTime = Carbon::createFromFormat('H:i:s', $checkOutTime)->setDateFrom($date);

                    $lateSeconds = $checkInDateTime->gt($shiftStart) ? $checkInDateTime->diffInSeconds($shiftStart) : 0;

                    if ($checkInDateTime->lt($shiftStart)) {
                        $checkInDateTime = $shiftStart->copy();
                    }

                    if ($checkOutDateTime->gt($shiftEndPlus30)) {
                        $checkOutDateTime = $shiftEndPlus30->copy();
                    }

                    $workingDuration = min($checkOutDateTime->diffInSeconds($checkInDateTime), 34600);

                    $attendance->update([
                        'late_seconds' => $lateSeconds,
                        'work_dur' => $workingDuration,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error refreshing attendance for employee {$schedule->num_sch} on date {$schedule->date}: " . $e->getMessage());
                    continue;
                }
            }
        }

        return redirect()->route('attendance.index')->with('success', 'Data absensi telah diperbarui dengan jadwal terbaru.');
    }

    public function cancelImportAttendance()
    {
        // Logic to clear session or handle cancel action
        return redirect()->route('attendance.index')->with('cancel', 'Data absensi tidak jadi di-import');
    }
}

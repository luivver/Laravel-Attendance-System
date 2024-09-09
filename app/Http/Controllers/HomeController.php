<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
// use App\Models\Cuti;
use App\Models\Leave;
// use App\Models\Employee;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class HomeController extends Controller
{
    public function index()
    {
        $currentYear = Carbon::now()->year;

        $lateCount = Attendance::whereYear('date', $currentYear)
            ->where('late_seconds', '>', 0)->count();
        $quotaCount = Attendance::whereYear('date', $currentYear)
            ->where('work_dur', '<', 32400)->count();
        $totalIzin = Leave::whereYear('start_date', $currentYear)
            ->where('status', 'acc')->count();

        [$labels, $datasets] = $this->lateChartYear();
        [$labels_m, $datasets_m] = $this->lateChartMonth();

        // Retrieve absentees hari ini
        $today = Carbon::today();
        $absentees = Leave::with('employee')
            ->whereIn('status', ['acc', 'waiting'])
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->paginate(3);

        return view('homepage.home', compact(
            'currentYear',
            'lateCount',
            'quotaCount',
            'totalIzin',
            'labels',
            'datasets',
            'labels_m',
            'datasets_m',
            'today',
            'absentees'
        ));
    }

    public function profile()
    {
        $logs = Activity::where('causer_id', auth()->id())->latest()->get();
        // dd($logs);
        return view('homepage.profile', compact('logs'));
    }

    public function late()
    {
        $currentYear = Carbon::now()->year;
        $attendances = Attendance::whereYear('date', $currentYear)->where('late_seconds', '>', 0)->paginate(20);
        return view('homepage.late', compact('attendances', 'currentYear'));
    }

    public function quota()
    {
        $currentYear = Carbon::now()->year;
        $attendances = Attendance::whereYear('date', $currentYear)->where('work_dur', '<', 32400)->paginate(20);
        return view('homepage.quota', compact('attendances', 'currentYear'));
    }

    public function izin()
    {
        $currentYear = Carbon::now()->year;
        $leaves = Leave::whereYear('start_date', $currentYear)->where('status', 'acc')->paginate(20);
        return view('homepage.izin', compact('leaves', 'currentYear'));
    }

    public function downloadLateRecordsCsv()
    {
        $currentYear = Carbon::now()->year;
        // Ambil data yang telat saja
        $data = Attendance::whereYear('date', $currentYear)->where('late_seconds', '>', 0)->get();
        $filename = "karyawan_terlambat_{$currentYear}.csv";
        $fp = fopen($filename, 'w+');

        fputcsv($fp, [
            'Tanggal',
            'Nama',
            'Nomor Karyawan',
            'Check-In',
            'Check-Out',
            'Status Kehadiran',
            'Waktu Keterlambatan',
            'Status Kuota Harian',
            'Waktu Kuota Harian',
        ]);

        foreach ($data as $row) {
            $lateSeconds = $row->late_seconds;
            $hours = floor($lateSeconds / 3600);
            $minutes = floor(($lateSeconds % 3600) / 60);
            $seconds = $lateSeconds % 60;
            $formattedLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            $statusKehadiran = "Terlambat";
            $statusKuota = $row->work_dur < 32400 ? 'Tidak Memenuhi Kuota' : 'Memenuhi Kuota';
            fputcsv($fp, [
                $row->date,
                $row->employee->name,
                $row->num_atd,
                $row->check_in,
                $row->check_out,
                $statusKehadiran,
                $formattedLate,
                $statusKuota,
                $row->work_dur,
            ]);
        }

        fclose($fp);

        $headers = [
            'Content-Type' => 'text/csv',
        ];

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties(['filename' => $filename])
            ->log('Admin mengunduh ' . $filename);

        return response()->download($filename, $filename, $headers);
    }

    public function downloadQuotaRecordsCsv()
    {
        $currentYear = Carbon::now()->year;
        $data = Attendance::whereYear('date', $currentYear)->where('work_dur', '<', 32400)->get();
        $filename = "karyawan_tidak_memenuhi_kuota_{$currentYear}.csv";
        $fp = fopen($filename, 'w+');

        fputcsv($fp, [
            'Tanggal',
            'Nama',
            'Nomor Karyawan',
            'Check-In',
            'Check-Out',
            'Status Kehadiran',
            'Waktu Keterlambatan',
            'Status Kuota Harian',
            'Waktu Kuota Harian'
        ]);

        foreach ($data as $row) {
            $lateSeconds = $row->late_seconds;
            $hours = floor($lateSeconds / 3600);
            $minutes = floor(($lateSeconds % 3600) / 60);
            $seconds = $lateSeconds % 60;
            $formattedLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            $statusKehadiran = $row->late_seconds > 0 ? 'Terlambat' : 'Tepat Waktu';
            $statusKuota = 'Tidak Memenuhi Kuota';
            fputcsv($fp, [
                $row->date,
                $row->employee->name,
                $row->num_atd,
                $row->check_in,
                $row->check_out,
                $statusKehadiran,
                $formattedLate,
                $statusKuota,
                $row->work_dur
            ]);
        }

        fclose($fp);

        $headers = [
            'Content-Type' => 'text/csv',
        ];

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties(['filename' => $filename])
            ->log('Admin mengunduh ' . $filename);

        return response()->download($filename, $filename, $headers);
    }

    public function downloadLeaveRecordsCsv()
    {
        $currentYear = Carbon::now()->year;
        $data = Leave::whereYear('start_date', $currentYear)->where('status', 'acc')->get();
        $filename = "karyawan_izin_{$currentYear}.csv";
        $fp = fopen($filename, 'w+');

        fputcsv($fp, [
            'Nama',
            'Nomor Karyawan',
            'Tgl Mulai',
            'Tgl Akhir',
            'Waktu Mulai',
            'Waktu Akhir',
            'Jenis Izin',
            'Alasan'
        ]);

        foreach ($data as $row) {
            fputcsv($fp, [
                $row->employee->name,
                $row->num_lvs,
                $row->start_date,
                $row->end_date,
                $row->start_time,
                $row->end_time,
                $row->jenis_izin,
                $row->reason,
            ]);
        }

        fclose($fp);

        $headers = [
            'Content-Type' => 'text/csv',
        ];

        activity()
            ->causedBy(auth()->user())
            // ->useLogName('Attendance Log')
            ->withProperties(['filename' => $filename])
            ->log('Admin mengunduh ' . $filename);

        return response()->download($filename, $filename, $headers);
    }

    public function lateChartYear()
    {
        $late_attend = Attendance::selectRaw('MONTH(date) as month, COUNT(*) as count')
            ->whereYear('date', date('Y'))
            ->where('late_seconds', '>', 0)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#0A5485', '#1a5f8d'];

        for ($i = 1; $i <= 12; $i++) {
            $month = date('F', mktime(0, 0, 0, $i, 1));
            $count = 0;

            foreach ($late_attend as $late) {
                if ($late->month == $i) {
                    $count = $late->count;
                    break;
                }
            }

            array_push($labels, $month);
            array_push($data, $count);
        }

        $datasets = [
            [
                'label' => 'Karyawan Terlambat Tahun Ini',
                'data' => $data,
                'backgroundColor' => $colors,
            ],
        ];

        return [$labels, $datasets];
    }

    public function lateChartMonth()
    {
        $late_attend_m = Attendance::selectRaw('DAY(date) as day, COUNT(*) as count')
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->where('late_seconds', '>', 0)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $labels_m = [];
        $data_m = [];
        $colors_m = ['#0A5485', '#1a5f8d'];

        for ($i = 1; $i <= 31; $i++) {
            $count_m = 0;

            foreach ($late_attend_m as $late_m) {
                if ($late_m->day == $i) {
                    $count_m = $late_m->count;
                    break;
                }
            }

            array_push($labels_m, $i);
            array_push($data_m, $count_m);
        }

        $datasets_m = [
            [
                'label' => 'Karyawan Terlambat Bulan Ini',
                'data' => $data_m,
                'backgroundColor' => $colors_m,
            ],
        ];

        return [$labels_m, $datasets_m];
    }
}

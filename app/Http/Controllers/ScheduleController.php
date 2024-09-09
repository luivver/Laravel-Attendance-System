<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Schedule;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve filter values
        $shiftDateFilter = $request->input('filter_tgl_shift');
        $workDaysFilter = $request->input('work_days');
        $departmentFilter = $request->input('department');

        // Set default schedules for employees without schedules
        $this->setDefaultSchedule();

        // Base query to fetch employees
        $employeesQuery = Employee::query();

        // Eager load schedules with date filter if provided
        if ($shiftDateFilter) {
            $employeesQuery->with(['schedules' => function ($query) use ($shiftDateFilter) {
                $query->whereDate('date', $shiftDateFilter)
                    ->select('id', 'num_sch', 'date', 'work_days', 'shift_start', 'shift_end');
            }]);
        } else {
            $employeesQuery->with('schedules');
        }

        // Filter based on work_days
        if ($workDaysFilter) {
            $employeesQuery->whereHas('schedules', function ($query) use ($workDaysFilter) {
                if ($workDaysFilter == 'Senin-Sabtu') {
                    $query->whereIn(DB::raw('DAYOFWEEK(date)'), [2, 3, 4, 5, 6, 7]); // Adjusted to match DAYOFWEEK in MySQL
                } elseif ($workDaysFilter == 'Senin-Jumat') {
                    $query->whereIn(DB::raw('DAYOFWEEK(date)'), [2, 3, 4, 5, 6]);
                }
            });
        }

        // Filter based on department
        if ($departmentFilter) {
            $employeesQuery->where('department', $departmentFilter);
        }

        // Execute the query and paginate results
        $employees = $employeesQuery->get(); // Using pagination instead of get()

        // Retrieve distinct departments for filter options
        $departments = Employee::select('department')->distinct()->pluck('department');
        // dd($employees->toArray());
        return view('schedules.index', compact('employees', 'departments'));
    }

    public function store(Request $request)
    {
        // ambil input nomor karyawan dan pecah menjadi array berdasarkan koma
        $employeeIds = explode(',', $request->input('input_num_sch'));

        // trim setiap ID untuk menghapus spasi yang tidak perlu
        $employeeIds = array_map('trim', $employeeIds);

        // validasi apakah setiap employee_num ada di tabel employees
        $employees = Employee::whereIn('employee_num', $employeeIds)->get();

        if ($employees->isEmpty()) {
            throw ValidationException::withMessages([
                'num_sch' => 'ID Karyawan tidak terdapat di database',
            ]);
        }

        // ambil data input dari form
        $startShift = $request->input('tgl_shift_start');
        $endShift = $request->input('tgl_shift_end');
        $workDays = $request->input('work_days');
        $shiftStart = $request->input('shift_start');
        $shiftEnd = $request->input('shift_end');

        // dd($workDays, $shiftStart, $shiftEnd);

        // set hari kerja berdasarkan pilihan
        $daysOfWeek = ($workDays == 'Senin-Jumat') ? [1, 2, 3, 4, 5] : [1, 2, 3, 4, 5, 6];

        // loop melalui rentang tanggal yang dipilih untuk memperbarui jadwal
        $startDate = Carbon::parse($startShift);
        $endDate = Carbon::parse($endShift);

        // Update jadwal karyawan sesuai input
        foreach ($employees as $employee) {
            $currentDate = clone $startDate;
            while ($currentDate->lte($endDate)) {
                $dayOfWeek = $currentDate->dayOfWeek;

                // debugging sebelum menyimpan ke database
                // dd('Menyimpan ke database:', [
                //     'num_sch' => $employee->employee_num,
                //     'date' => $currentDate->toDateString(),
                //     'shift_start' => in_array($dayOfWeek, $daysOfWeek) ? $shiftStart : null,
                //     'shift_end' => in_array($dayOfWeek, $daysOfWeek) ? $shiftEnd : null,
                //     'work_days' => $daysOfWeek
                // ]);

                Schedule::updateOrCreate(
                    [
                        'num_sch' => $employee->employee_num,
                        'date' => $currentDate->toDateString()
                    ],
                    [
                        'shift_start' => in_array($dayOfWeek, $daysOfWeek) ? $shiftStart : null,
                        'shift_end' => in_array($dayOfWeek, $daysOfWeek) ? $shiftEnd : null,
                        'work_days' => $workDays
                    ]
                );

                $currentDate->addDay();
            }
        }

        // Gabungkan ID karyawan menjadi string
        $employeeIdsString = implode(', ', $employeeIds);

        activity()
            ->causedBy(auth()->user())
            ->log('Admin mengubah jadwal karyawan ' . $employeeIdsString);

        return redirect()->route('schedules.index')->with('success', 'Berhasil update jadwal!');
    }

    private function setDefaultSchedule()
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $currentDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            while ($currentDate->lte($endDate)) {
                $dayOfWeek = $currentDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

                Schedule::firstOrCreate(
                    [
                        'num_sch' => $employee->employee_num,
                        'date' => $currentDate->toDateString()
                    ],
                    [
                        'shift_start' => in_array($dayOfWeek, [1, 2, 3, 4, 5]) ? '08:00:00' : null,
                        'shift_end' => in_array($dayOfWeek, [1, 2, 3, 4, 5]) ? '17:00:00' : null,
                        'work_days' => 'Senin-Jumat'
                    ]
                );

                $currentDate->addDay();
            }
        }
    }
}

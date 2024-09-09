<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Cuti;
// use App\Models\Attendance;
// use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index()
    {
        $cuti = new Cuti();
        $cuti->generateCuti();
        // $cuti->updateCutiValues();

        $employees = Employee::with('cuti')->get();

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        // $fileName = 'karyawan_' . now()->format('dmY_his') . '.' . $file->getClientOriginalExtension();
        // $file->storeAs('employees', $fileName);

        $contents = file_get_contents($file);
        $lines = preg_split('/\r\n|\r|\n/', $contents);

        $extension = $file->getClientOriginalExtension();
        $delimiter = ($extension === 'txt') ? "\t" : ",";

        $isFirstLine = true;
        foreach ($lines as $line) {
            if ($isFirstLine) {
                $isFirstLine = false;
                continue;
            }

            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $data = str_getcsv($line, $delimiter);

            if (count($data) >= 10) {
                $hariPertama = Carbon::createFromFormat('m/d/Y', $data[9]);
                // Update or create the employee record with the new calculations
                Employee::updateOrCreate(
                    [
                        'employee_num' => $data[0],
                        'no_rek' => $data[3],
                        'npwp' => $data[4],
                        'nik' => $data[5]
                    ],
                    [
                        'name' => $data[1],
                        'gender' => $data[2],
                        'location' => $data[6],
                        'department' => $data[7],
                        'position' => $data[8],
                        'hari_pertama' => $hariPertama,
                    ]
                );
            }
        }

        return redirect()->route('employees.index')->with('success', 'File berhasil diupload');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }

    public function downloadEmployeeCsv()
    {
        $data = Employee::all();
        $filename = 'rekap_data_karyawan_' . now()->format('dmY') . '.csv';
        $fp = fopen($filename, 'w+');

        fputcsv($fp, array(
            'No.',
            'Nama',
            'Jenis kelamin',
            'No. Karyawan',
            'NIK',
            'NPWP',
            'No. Rek',
            'Lokasi',
            'Departemen',
            'Posisi',
            'Hari Pertama',
            'Hari Kerja',
            'Hari Makan',
            'Pot Terlambat',
            'Pot Kuota Kerja',
            'Pot Cuti',
            'Cuti',
            'Izin',
            'Sakit',
        ));

        foreach ($data as $index => $row) {
            $lateSeconds = $row->total_late_seconds;
            $hours = floor($lateSeconds / 3600);
            $minutes = floor(($lateSeconds % 3600) / 60);
            $seconds = $lateSeconds % 60;
            $formattedLate = sprintf('%02d : %02d : %02d', $hours, $minutes, $seconds);

            $fulfilledQuota = $row->sum_work_time;
            $fqhours = floor($fulfilledQuota / 3600);
            $fqminutes = floor(($fulfilledQuota % 3600) / 60);
            $fqseconds = $fulfilledQuota % 60;
            $formattedFulfilledQuota = sprintf('%02d:%02d:%02d', $fqhours, $fqminutes, $fqseconds);

            fputcsv($fp, array(
                $index + 1,
                $row->name,
                $row->gender,
                $row->employee_num,
                $row->nik,
                $row->npwp,
                $row->no_rek,
                $row->location,
                $row->department,
                $row->position,
                $row->hari_pertama,
                $row->total_hari,
                $row->total_hari,
                $formattedLate,
                $formattedFulfilledQuota,
                $row->cuti->curr_pot_cuti,
                $row->cuti_days,
                $row->izin_days,
                $row->sakit_days,
            ));
        }

        fclose($fp);

        return response()->download($filename, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function getEmployeeDetails($employee_num)  // untuk di Leave input
    {
        $employee = Employee::where('employee_num', $employee_num)->firstOrFail();
        // dd($employee);

        if ($employee) {
            return response()->json([
                'name' => $employee->name,
                'position' => $employee->position,
            ]);
        } else {
            return response()->json(['message' => 'Employee not found'], 404);
        }
    }
}

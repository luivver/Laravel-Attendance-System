<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Log;
// use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $leaveQuery = Leave::with('employee');
        // data karyawan terkait diambil bersamaan dengan data izin. 
        if ($request->has('year') && !empty($request->input('year'))) {
            $leaveQuery->whereYear('start_date', $request->input('year'));
        }

        if ($request->has('month') && !empty($request->input('month'))) {
            $leaveQuery->whereMonth('start_date', $request->input('month'));
        }

        if ($request->has('date') && !empty($request->input('date'))) {
            $leaveQuery->whereDay('start_date', $request->input('date'));
        }

        $leaves = $leaveQuery->paginate(40);

        return view('leaves.index', compact('leaves'));
    }

    // public function create()
    // {
    //     return view('leaves.create');
    // }

    public function store(Request $request)
    {
        // validasi data
        $request->validate([
            'input_id_izin' => 'required|exists:employees,employee_num',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',
            'jenis_izin' => [
                'required',
                'string',
                Rule::in(['Sakit', 'Izin', 'Cuti', 'Terlambat', 'Meninggalkan Kantor', 'Lainnya']),
            ],
            'reason' => 'required|string|max:255',
            'no_telp' => 'required|string|max:14',
            'status' => 'required|in:acc,waiting,decline',
        ]);

        $employee = Employee::where('employee_num', $request->input('input_id_izin'))->firstOrFail();

        // setelah validasi ok smua maka baru dimasukkan ke database
        Leave::create([
            'num_lvs' => $employee->employee_num,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'jenis_izin' => $request->input('jenis_izin'),
            'reason' => $request->input('reason'),
            'no_telp' => $request->input('no_telp'),
            'status' => $request->input('status'),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->log('Admin menyimpan form izin karyawan');

        // Redirect back with a success message
        return redirect()->route('leaves.index')->with('success', 'Izin Karyawan ' . $employee->employee_num . ' berhasil disimpan!');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the status input
        $request->validate([
            'status' => 'required|in:acc,waiting,decline',
        ]);

        // Find the leave record by ID
        $leave = Leave::findOrFail($id);

        // Update the status
        $leave->update([
            'status' => $request->input('status'),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->log('Admin mengubah status izin karyawan ' . $leave->num_lvs);

        // Redirect back with a success message
        return redirect()->route('leaves.index')->with('success', 'Status izin ' . $leave->employee_num . ' berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);  // Pastikan 'employee' adalah nama relasi yang benar

        // hapus record izin dari database
        $leave->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Admin menghapus izin karyawan ' . $leave->num_lvs);

        return redirect()->route('leaves.index')->with('success', 'Izin karyawan ' . $leave->employee_num . ' berhasil dihapus!');
    }

    // show view yang akan diprint nanti
    public function show($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);
        return view('leaves.printPreview', compact('leave'));
    }
    public function printShow($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);
        
        activity()
            ->causedBy(auth()->user())
            ->log('Admin mencetak izin karyawan ' . $leave->num_lvs);

        return view('leaves.print', compact('leave'));
    }

    public function showTelat($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);
        return view('leaves.printPreviewTelat', compact('leave'));
    }
    public function printShowTelat($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);
        // dd($leave);

        activity()
            ->causedBy(auth()->user())
            ->log('Admin mencetak izin karyawan ' . $leave->num_lvs);

        return view('leaves.printTelat', compact('leave'));
    }

    public function downloadIzin(Request $request)
    {
        $query = Leave::with('employee')->latest(); // Include the related employee data

        // Filter berdasarkan tahun
        if ($request->has('year') && !empty($request->input('year'))) {
            $query->whereYear('start_date', $request->input('year')); // Corrected the field name to 'start_date'
        }

        // Filter berdasarkan bulan
        if ($request->has('month') && !empty($request->input('month'))) {
            $query->whereMonth('start_date', $request->input('month')); // Corrected the field name to 'start_date'
        }

        // Filter berdasarkan tanggal
        if ($request->has('date') && !empty($request->input('date'))) {
            $query->whereDay('start_date', $request->input('date')); // Corrected the field name to 'start_date'
        }

        $data = $query->get();

        // Penamaan file berdasarkan filter
        $filename = "rekap_izin";
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

        $fp = fopen($filename, 'w+');

        // Menulis header CSV
        fputcsv($fp, [
            'Nama',
            'Nomor Karyawan',
            'Tanggal Mulai',
            'Tanggal Akhir',
            'Waktu Mulai',
            'Waktu Akhir',
            'Jenis Izin',
            'Alasan',
            'No. Telp',
            'Status'
        ]);

        // Menulis data ke dalam CSV
        foreach ($data as $row) {
            fputcsv($fp, [
                $row->employee->name, // Retrieve the name from the related employee
                $row->employee->employee_num, // Retrieve the employee number
                $row->start_date->format('d-m-Y'), // Format date to 'd-m-Y'
                $row->end_date->format('d-m-Y'), // Format date to 'd-m-Y'
                $row->start_time->format('H:i:s'), // Format time to 'H:i:s'
                $row->end_time->format('H:i:s'), // Format time to 'H:i:s'
                $row->jenis_izin,
                $row->reason,
                $row->no_telp,
                ucfirst($row->status) // Capitalize the first letter of the status
            ]);
        }

        fclose($fp);

        $headers = [
            'Content-Type' => 'text/csv',
        ];

        activity()
            ->causedBy(auth()->user())
            ->withProperties(['filters' => $request->all(), 'filename' => $filename])
            ->log('Admin mengunduh ' . $filename);

        return response()->download($filename, $filename, $headers);
    }
}

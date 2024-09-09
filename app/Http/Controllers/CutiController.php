<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index()
    {
        $cuti = new Cuti();
        $cuti->generateCuti();
        $cuti->moveCurrToTemp();

        $cutis = Cuti::all();

        return view('cuti.index', compact('cutis'));
    }

    public function updateExpDate(Request $request, $id)
    {
        $request->validate([
            'exp_temp' => 'required|date',
        ]);

        $cuti = Cuti::findOrFail($id);

        $cuti->exp_temp_cuti = $request->input('exp_temp');
        $cuti->save();

        activity()
            ->causedBy(auth()->user())
            ->log('Admin mengubah tanggal expired temporary cuti ' . $cuti->num_cuti);

        return redirect()->back()->with('success', 'Expiration date updated successfully.');
    }
}

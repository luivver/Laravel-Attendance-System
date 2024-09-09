<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // Import Str
use Illuminate\Support\Facades\Mail; // Import Mail
use Illuminate\Support\Facades\DB; // Import DB

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('log.register');
    }

    public function register(Request $request)
    {
        // Validasi data input
        $request->validate([
            'name' => 'required|string|max:85|min:3',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Simpan data ke database
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Redirect atau beri pesan sukses
        return redirect('/')->with('msg', 'Registrasi berhasil! Silahkan login.');
    }
}

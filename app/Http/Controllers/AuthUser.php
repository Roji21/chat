<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;

class AuthUser extends Controller
{
    public function index(){
        return view('login_page');
    }
    public function login(Request $request){
        // 1. Validasi input dari form client
        $credentials = $request->validate([
            'login_input' => 'required|string', // Bisa diisi email atau username
            'password' => 'required|string',
        ]);
        // 2. Tentukan apakah input berupa email atau username
        $fieldType = filter_var($request->login_input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Susun data untuk dicocokkan oleh sistem Laravel Auth
        $loginData = [
            $fieldType => $request->login_input,
            'password' => $request->password
        ];

        // 4. Proses Autentikasi (Laravel otomatis mengecek enkripsi password di database)
        if (Auth::attempt($loginData)) {
            // Jika sukses, buat ulang session agar aman dari session fixation hacking
            $request->session()->regenerate();

            // Alihkan user ke halaman dashboard
            return redirect()->intended('/main');
        }

        // 5. Jika gagal, kembalikan ke halaman login dengan pesan eror
        return back()->withErrors([
            'login_input' => 'Username/Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('login_input');
    }
    public function logout(\Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

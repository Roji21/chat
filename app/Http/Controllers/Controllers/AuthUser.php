<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\AuthManager;
use App\Models\CustomUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Notifications\SendOtpEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;


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
            $existingUser = CustomUser::where($fieldType, $request->login_input)->first();
            if ($existingUser && ($existingUser->email_verified_at == 0 || is_null($existingUser->email_verified_at))) {
                // Jika user sudah ada tapi belum diverifikasi, perbarui OTP dan waktu kedaluwarsa
                $otp = rand(100000, 999999);
                $existingUser->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => now()->addMinutes(10),
                ]);
                // Kirim ulang OTP via WhatsApp atau Email
                $this->sendEmailOtp($existingUser->email, $otp);

                // Simpan ID user di session untuk proses verifikasi di halaman berikutnya
                session(['otp_user_id' => $existingUser->id]);

                return redirect()->route('otp.verify.page')->with('success', 'Akun belum terverifikasi silahkan masukan kode OTP.Kode OTP telah dikirim ulang ke email Anda.');
            }
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
    public function register(){
        return view('register_page');
    }
    public function checkUsername(Request $request)
    {
        $username = $request->input('username');

        // 1. Validasi format di sisi server (5 - 11 karakter, alfanumerik)
        if (!preg_match('/^[a-zA-Z0-9]{5,11}$/', $username)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Username harus 5-11 karakter (huruf & angka).'
            ]);
        }

        // 2. Cek apakah username sudah ada di database
        $isTaken = CustomUser::where('username', $username)->exists();

        if (!$isTaken) {
            return response()->json([
                'status' => 'available',
                'message' => 'Username tersedia!'
            ]);
        }

        // 3. Jika sudah ada, buat 3 saran username alternatif yang unik
        $suggestions = [];
        while (count($suggestions) < 3) {
            // Tambahkan angka acak 2-3 digit di belakang username inputan
            $candidate = $username . rand(10, 999);

            // Potong maksimal 11 karakter jika terlalu panjang
            $candidate = substr($candidate, 0, 11); 

            // Pastikan kandidat tersebut belum ada di database dan belum masuk daftar saran
            $checkCandidate = CustomUser::where('username', $candidate)->exists();

            if (!$checkCandidate && !in_array($candidate, $suggestions)) {
                $suggestions[] = $candidate;
            }
        }

        return response()->json([
            'status' => 'taken',
            'message' => 'Username sudah digunakan.',
            'suggestions' => $suggestions
        ]);
    }
    // 1. Proses Pendaftaran & Kirim OTP
    public function registerup(Request $request)
    {
        $existingUser = CustomUser::where('email', $request->email)->first();
        if ($existingUser ) {
            return back()->withErrors(['password' => 'Akun sudah terdaftar silahkan lakukan log in.'])->withInput();    
        }

        $request->validate([
            'username' => 'required|string|unique:custom_users,username',
            'email' => 'required|email|unique:custom_users,email',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
        ],[
        // Kustomisasi pesan error dalam Bahasa Indonesia
        'email.unique'  => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.',
        ]);
        if ($request->password !== $request->password_confirmation) {
            return back()->withErrors(['password' => 'Konfirmasi password tidak cocok.'])->withInput();
        }

        // Generate 6 digit angka acak
        $otp = rand(100000, 999999); 
        $expiresAt = Carbon::now()->addMinutes(10);

        // Simpan user dengan status belum aktif/terverifikasi
        $user = CustomUser::create([
            'username' => $request->username,
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
            'email_verified_at' => '0', 
        ]);

        // Kirim OTP via WhatsApp
        $this->sendEmailOtp($user->email, $otp);

        // Simpan ID user di session untuk proses verifikasi di halaman berikutnya
        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.verify.page')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    private function sendEmailOtp($email, $otp)
    {
        // Kirim email menggunakan class mailable yang sudah dibuat
        Mail::to($email)->send(new SendOtpMail($otp));
    }

    // 3. Tampilkan halaman input OTP bergaya WhatsApp
    public function showOtpPage()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('register_page.index')->with('error', 'Silakan daftar terlebih dahulu.');
        }
        return view('auth.verify-otp');
    }

    // 4. Proses Validasi OTP yang dimasukkan User
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        $user = CustomUser::find($userId);

        if (!$user) {
            return redirect()->route('register_page.index')->with('error', 'User tidak ditemukan.');
        }

        // Cek apakah OTP cocok dan belum kedaluwarsa
        if ($user->otp_code === $request->otp && Carbon::now()->isBefore($user->otp_expires_at)) {
            
            // Verifikasi sukses: bersihkan kolom OTP dan isi email_verified_at (atau buat kolom baru phone_verified_at)
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
                'email_verified_at' => '1', 
            ]);

            // Login otomatis
            auth()->login($user);
            session()->forget('otp_user_id');

            return redirect('/main')->with('success', 'Akun berhasil diverifikasi!');
        }

        return back()->withErrors(['otp' => 'Kode OTP salah atau telah kedaluwarsa.']);
    }
}

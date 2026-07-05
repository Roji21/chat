<?php

namespace App\Models;

// Ubah baris extend ini
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;

class CustomUser extends Authenticatable // Menggunakan Authenticatable
{
    use HasFactory;
    use Notifiable;

    // Sembunyikan password agar tidak tidak sengaja bocor saat query data
    protected $hidden = ['password']; 
    
    protected $fillable = [
        'number', 'username', 'email', 'name', 'password', 
        'otp_code', 'otp_expires_at', 'email_verified_at'
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            // if (empty($user->number)) {
                $user->number = '021' . rand(10000000, 99999999);
                // $daftar_foto = ['foto1.jpeg', 'foto2.jpeg', 'foto3.jpeg', 'foto4.jpeg'];
                // $user->photo = $daftar_foto[rand(0, 3)];
            // }
        });
    }
}

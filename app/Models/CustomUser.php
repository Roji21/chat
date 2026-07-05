<?php

namespace App\Models;

// Ubah baris extend ini
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomUser extends Authenticatable // Menggunakan Authenticatable
{
    use HasFactory;

    protected $fillable = ['number', 'username', 'email', 'name', 'password', 'about', 'photo'];

    // Sembunyikan password agar tidak tidak sengaja bocor saat query data
    protected $hidden = ['password']; 

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->number = '021' . rand(10000000, 99999999);
        });
    }
}

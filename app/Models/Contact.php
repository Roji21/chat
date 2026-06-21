<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['user_number', 'contact_number', 'custom_name', 'status'];

    protected static function boot()
    {
        parent::boot();

        // Validasi otomatis saat data kontak akan dibuat
        static::creating(function ($contact) {
            if ($contact->user_number === $contact->contact_number) {
                // Gagalkan proses simpan jika user mencoba menambahkan nomornya sendiri
                throw new \Exception("User number and contact number cannot be the same.");
            }
        });
    }
}

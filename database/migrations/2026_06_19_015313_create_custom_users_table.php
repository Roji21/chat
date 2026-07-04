<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_users', function (Blueprint $table) {
            $table->id(); // Auto-increment
            $table->string('number', 11)->unique(); // Menyimpan format 021XXXXX (Total 11 karakter)
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->text('about')->nullable();
            $table->string('email_verified_at');
            // === KOLOM TAMBAHAN UNTUK FITUR OTP (GAYA WHATSAPP) ===
            // Menyimpan 6 digit kode OTP sementara
            $table->string('otp_code', 6)->nullable();
            // Batas waktu kedaluwarsa kode OTP (misal: 10 menit setelah dikirim)
            $table->timestamp('otp_expires_at')->nullable();
            // Kolom token untuk fitur "Remember Me" saat login
            $table->rememberToken();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_users');
    }
};

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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender', 11);     // Nomor pengirim (Foreign Key)
            $table->string('recipient', 11);  // Nomor penerima (Foreign Key)
            $table->text('message_text');     // Isi pesan (saya tambahkan agar tabel berguna)
            $table->timestamp('sent_time')->useCurrent(); // Waktu kirim otomatis saat ini
            $table->timestamps(); // create_at dan updated_at bawaan laravel

            // Aturan Relasi ke tabel custom_users
            $table->foreign('sender')->references('number')->on('custom_users')->onDelete('cascade');
            $table->foreign('recipient')->references('number')->on('custom_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

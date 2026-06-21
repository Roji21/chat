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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Menghubungkan ke kolom 'number' di tabel 'custom_users'
            $table->string('user_number', 11);
            $table->string('contact_number', 11);

            $table->string('custom_name');

            // Status: 0 = blokir, 1 = aktif, 2 = favorite
            $table->tinyInteger('status')->default(1); 
            $table->timestamps();

            // Membuat aturan Foreign Key agar data sinkron dengan tabel user
            $table->foreign('user_number')->references('number')->on('custom_users')->onDelete('cascade');
            $table->foreign('contact_number')->references('number')->on('custom_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

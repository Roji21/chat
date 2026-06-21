<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // 1. Buat user terlebih dahulu (wajib ada nomor user)
        \App\Models\CustomUser::factory(10)->create();

        // 2. Baru buat data kontak (akan otomatis mengambil nomor user di atas)
        \App\Models\Contact::factory(15)->create();
        
        // 3. Buat 30 data riwayat Pesan acak
        \App\Models\Message::factory(30)->create();
    }
}

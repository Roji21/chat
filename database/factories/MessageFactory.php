<?php

namespace Database\Factories;

use App\Models\CustomUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        // Ambil semua nomor user yang tersedia
        $users = CustomUser::pluck('number')->toArray();

        $sender = fake()->randomElement($users) ?? '0211111111';
        // Pastikan penerima bukan si pengirim itu sendiri
        $recipient = fake()->randomElement(array_diff($users, [$sender])) ?? '0212222222';

        return [
            'sender' => $sender,
            'recipient' => $recipient,
            'message_text' => fake()->sentence(), // Membuat 1 kalimat pesan acak
            'sent_time' => now()->subMinutes(rand(1, 1000)), // Waktu kirim diacak ke beberapa jam lalu
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\CustomUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        // Mengambil semua user yang ada untuk diacak nomornya
        $users = CustomUser::pluck('number')->toArray();

        // Jika data user kosong, isi default (untuk mencegah eror saat seeding)
        $userNumber = fake()->randomElement($users) ?? '0211111111';
        
        // Ambil contact_number acak yang TIDAK SAMA dengan userNumber
        $contactNumber = fake()->randomElement(array_diff($users, [$userNumber])) ?? '0212222222';

        return [
            'user_number' => $userNumber,
            'contact_number' => $contactNumber,
            'custom_name' => fake()->name(),
            'status' => fake()->randomElement([0, 1, 2]), // Mengisi angka 0, 1, atau 2
        ];
    }
}

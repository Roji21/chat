<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CustomUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numerify('##########'), // Menghasilkan 10 digit angka unik
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'password' => Hash::make('password123'),
            'about' => fake()->paragraph(),
            'photo' => 'default.jpeg', // (Opsional) typo diperbaiki dari .jepg ke .jpeg
        ];
    }
}

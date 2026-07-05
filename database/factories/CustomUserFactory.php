<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CustomUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Kolom 'number' sengaja dikosongkan karena sudah di-handle oleh Model
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'password' => Hash::make('password123'), // Password dummy default
            'about' => fake()->paragraph(),
        ];
    }
}

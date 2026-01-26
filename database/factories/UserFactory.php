<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'fullname'          => fake()->name(),
            'username'          => fake()->unique()->userName(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),               // ← usually set for seeded users
            'password'          => Hash::make('password'), // or Str::password() in newer versions
            'role'              => $this->faker->randomElement(['admin', 'client']),
            'is_verified'       => true,                // ← most important change
            // or: 'is_verified' => $this->faker->boolean(80),  // 80% chance verified
            'remember_token'    => Str::random(10),
        ];
    }

    // If you have a "unverified" state already, update/add it
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'is_verified'       => false,           // ← add or update this
        ]);
    }

    // Optional: state for admin
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'        => 'admin',
            'is_verified' => true,
        ]);
    }
}
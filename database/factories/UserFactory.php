<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $password = 'admin';
        $hashedPassword = Hash::make($password);
        return [
            'nama_user' => 'Dio Putra',
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'email_verified_at' => now(),
            'password' => $hashedPassword, // password
            'remember_token' => Str::random(10),
            'no_hp' => '081331945656',
            'wa' => '081331945656',
            'pin' => '123456',
            'status_user' => 1,
            'created_by' => 'dio',
            'create_date' => now(),
            'update_by' => 'dio',
            'update_date' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {
        $gender = $this->faker->randomElement(['laki - laki', 'perempuan']);
        return [
            //
            'nama' => fake()->name(),
            'foto' => fake()->imageUrl($width = 400, $height = 400),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => fake()->phoneNumber(),
            'gender' => $gender,
            'tanggal_lahir' => $this->faker->date('Y_m_d'),
        ];
    }
}

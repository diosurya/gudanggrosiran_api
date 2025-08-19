<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alamat>
 */
class AlamatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $nama_alamat = $this->faker->randomElement(['rumah', 'rumah saudara', 'kantor']);
        return [
            //
            'id_customer' => mt_rand(1, 10),
            'nama_penerima' => fake()->name(),
            'nama_alamat' => $nama_alamat,
            'detail_alamat' => fake()->address(),
            'no_hp' => fake()->phoneNumber(),
            'kode_pos' => $this->faker->randomNumber(4, false),
        ];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => '18cfa5aa-4055-43f8-81f9-fdead1240404',
            'name' => 'Superadmin',
            'username' => 'admin',
            'password' => Hash::make('admin123123'),
            'email' => 'admin@mail.com',
            'phone' => '081234567890',
            'whatsapp' => '081234567890',
            'pin' => '123456',
            'id_type_user' => 1,
            'created_at' => '2025-08-20 05:47:46',
            'updated_at' => '2025-08-20 05:47:46',
        ]);
    }
}

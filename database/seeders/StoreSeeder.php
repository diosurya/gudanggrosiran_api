<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $samarindaId = (string) Str::uuid();
        $kupangId    = (string) Str::uuid();

         DB::table('stores')->insert([
            [
                'id'          => $samarindaId,
                'code'        => 'S-001',
                'name'        => 'Samarinda',
                'address'     => 'Jl Kemakmuran no 71',
                'district'    => 'Sungai Pindang Dalam',
                'city'        => 'Kota Samarinda',
                'province'    => 'Kalimantan Timur',
                'postal_code' => 75242,
                'email'       => 'gudanggrosiran1.samarinda@gmail.com',
                'phone'       => '081130776712',
                'latitude'    => -0.479071,
                'longitude'   => 117.164302,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => $kupangId,
                'code'        => 'S-002',
                'name'        => 'Kupang',
                'address'     => 'Jl. Terusan Timor Raya No.3, Oesapa, Kec. Klp. Lima, Kota Kupang, Nusa Tenggara Tim',
                'district'    => 'Kelapa Lima',
                'city'        => 'Kupang',
                'province'    => 'Nusa Tenggara Timur',
                'postal_code' => 85228,
                'email'       => '',
                'phone'       => '081130757550',
                'latitude'    => -10.157604,
                'longitude'   => 123.636996,
                'is_active'   => true, 
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}

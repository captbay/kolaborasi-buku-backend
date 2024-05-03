<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigWebSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // make data
        $data = [
            [
                'key' => 'no_rek',
                'tipe' => 'TEXT',
                'value' => '92031903901923',
            ],
            [
                'key' => 'bank_rek',
                'tipe' => 'TEXT',
                'value' => 'BANK SEJAHTERA',
            ],
            [
                'key' => 'nama_rek',
                'tipe' => 'TEXT',
                'value' => 'PT PENERBITAN BUKU',
            ],
        ];

        foreach ($data as $d) {
            DB::table('config_web')->insert([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'key' => $d['key'],
                'tipe' => $d['tipe'],
                'value' => $d['value'],
                'active_flag' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
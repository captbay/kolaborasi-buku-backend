<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KontenEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'file' => '/galeri_config_file/image/event_1.jpg',
            ],
            [
                'file' => '/galeri_config_file/image/event_2.jpg',
            ],
            [
                'file' => '/galeri_config_file/image/event_3.jpg',
            ],
            [
                'file' => '/galeri_config_file/image/event_4.jpg',
            ],
        ];

        foreach ($data as $d) {
            DB::table('konten_event')->insert([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'tipe' => 'IMAGE',
                'file' => $d['file'],
                'waktu_mulai' => now(),
                'waktu_selesai' => now()->addDays(120),
                'active_flag' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

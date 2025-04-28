<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = 'admin@penerbitarunika.com';
        
        DB::table('users')->insert([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'nama_depan' => 'Super',
            'nama_belakang' => 'Admin',
            'email' => $adminEmail,
            'password' => Hash::make('password'),
            'no_telepon' => '081234567890',
            'tgl_lahir' => now()->subYears(25)->toDateString(),
            'gender' => 'L',
            'alamat' => 'Alamat Admin',
            'provinsi' => 'DKI Jakarta',
            'kecamatan' => 'Kebayoran Baru',
            'kota' => 'Jakarta Selatan',
            'kode_pos' => '12345',
            'foto_profil' => null,
            'status_verif_email' => true,
            'email_verified_at' => now(),
            'role' => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

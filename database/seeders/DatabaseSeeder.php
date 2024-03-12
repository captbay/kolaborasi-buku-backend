<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\bab_buku_kolaborasi;
use App\Models\buku_dijual;
use App\Models\buku_kolaborasi;
use App\Models\buku_lunas_user;
use App\Models\buku_permohonan_terbit;
use App\Models\bukudijual_penulis_pivot;
use App\Models\config_web;
use App\Models\jasa_paket_penerbitan;
use App\Models\kategori;
use App\Models\keranjang;
use App\Models\konten_event;
use App\Models\konten_faq;
use App\Models\list_transaksi_buku;
use App\Models\mou;
use App\Models\paket_penerbitan;
use App\Models\penulis;
use App\Models\storage_buku_dijual;
use App\Models\testimoni_pembeli;
use App\Models\transaksi_kolaborasi_buku;
use App\Models\transaksi_paket_penerbitan;
use App\Models\transaksi_penjualan_buku;
use App\Models\User;
use App\Models\user_bab_buku_kolaborasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // make user admin
        User::create([
            'nama_depan' => 'admin',
            'nama_belakang' => 'penerbitan',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'no_telepon' => '081234567890',
            'status_verif_email' => 1,
            'email_verified_at' => now(),
            'role' => 'ADMIN',
        ]);

        User::factory(10)->create();
        kategori::factory(10)->create();

        // buku dijual
        penulis::factory(10)->create();
        buku_dijual::factory(20)->create();
        keranjang::factory(20)->create();
        testimoni_pembeli::factory(10)->create();
        bukudijual_penulis_pivot::factory(10)->create();
        buku_lunas_user::factory(20)->create();
        storage_buku_dijual::factory(10)->create();
        transaksi_penjualan_buku::factory(20)->create();
        list_transaksi_buku::factory(30)->create();

        // config web
        konten_event::factory(10)->create();
        konten_faq::factory(10)->create();
        config_web::factory(10)->create();

        // paket penerbitan
        paket_penerbitan::factory(10)->create();
        jasa_paket_penerbitan::factory(20)->create();
        buku_permohonan_terbit::factory(10)->create();
        transaksi_paket_penerbitan::factory(20)->create();

        // buku kolaborasi
        buku_kolaborasi::factory(10)->create();
        bab_buku_kolaborasi::factory(10)->create();
        user_bab_buku_kolaborasi::factory(10)->create();
        transaksi_kolaborasi_buku::factory(20)->create();

        // mou
        DB::table('mou')->insert([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'nama' => "MOU Template 2024",
            'deskripsi' => "template perjanjian kerjasama penerbitan buku yang dibutuhkan sebagai syarat penerbitan dilakukan sebagai sebuah kesepakatan antara penulis dan penerbit",
            'file_mou' => '/buku_final_temp.pdf',
            'active_flag' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // make notification seeder
        $data = [
            [
                'type' => 'Anda Berhasil Masuk',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => User::all()->random()->id,
                'data' => json_encode([
                    'title' => 'Selamat Datang',
                    'message' => 'Selamat datang di aplikasi penerbitan buku',
                ])
            ],
            [
                'type' => 'Anda Berhasil Masuk',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => User::all()->random()->id,
                'data' => json_encode([
                    'title' => 'Selamat Datang',
                    'message' => 'Selamat datang di aplikasi penerbitan buku',
                ])
            ],
            [
                'type' => 'Anda Berhasil Masuk',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => User::all()->random()->id,
                'data' => json_encode([
                    'title' => 'Selamat Datang',
                    'message' => 'Selamat datang di aplikasi penerbitan buku',
                ])
            ],
            [
                'type' => 'Anda Berhasil Masuk',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => User::all()->random()->id,
                'data' => json_encode([
                    'title' => 'Selamat Datang',
                    'message' => 'Selamat datang di aplikasi penerbitan buku',
                ])
            ],
            [
                'type' => 'Anda Berhasil Masuk',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => User::all()->random()->id,
                'data' => json_encode([
                    'title' => 'Selamat Datang',
                    'message' => 'Selamat datang di aplikasi penerbitan buku',
                ])
            ],
            [
                'type' => 'Anda Berhasil Masuk',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => User::all()->random()->id,
                'data' => json_encode([
                    'title' => 'Selamat Datang',
                    'message' => 'Selamat datang di aplikasi penerbitan buku',
                ])
            ],
            [
                'type' => 'Anda Berhasil Masuk',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => User::all()->random()->id,
                'data' => json_encode([
                    'title' => 'Selamat Datang',
                    'message' => 'Selamat datang di aplikasi penerbitan buku',
                ])
            ],
        ];

        foreach ($data as $d) {
            DB::table('notifications')->insert([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'type' => $d['type'],
                'notifiable_type' => $d['notifiable_type'],
                'notifiable_id' => $d['notifiable_id'],
                'data' => $d['data'],
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

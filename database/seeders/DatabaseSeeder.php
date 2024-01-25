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
use App\Models\kategori;
use App\Models\keranjang;
use App\Models\konten_event;
use App\Models\konten_faq;
use App\Models\list_transaksi_buku;
use App\Models\notifikasi;
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
            'role' => 'ADMIN',
            'active_flag' => 1,
        ]);

        User::factory(10)->create();
        kategori::factory(10)->create();

        // buku dijual
        penulis::factory(10)->create();
        buku_dijual::factory(10)->create();
        keranjang::factory(10)->create();
        testimoni_pembeli::factory(10)->create();
        bukudijual_penulis_pivot::factory(10)->create();
        buku_lunas_user::factory(10)->create();
        storage_buku_dijual::factory(10)->create();
        transaksi_penjualan_buku::factory(10)->create();
        list_transaksi_buku::factory(10)->create();

        // config web
        konten_event::factory(10)->create();
        konten_faq::factory(10)->create();
        config_web::factory(10)->create();

        // paket penerbitan
        paket_penerbitan::factory(10)->create();
        buku_permohonan_terbit::factory(10)->create();
        transaksi_paket_penerbitan::factory(10)->create();

        // buku kolaborasi
        buku_kolaborasi::factory(10)->create();
        bab_buku_kolaborasi::factory(10)->create();
        user_bab_buku_kolaborasi::factory(10)->create();
        transaksi_kolaborasi_buku::factory(10)->create();

        // notifikasi
        notifikasi::factory(10)->create();
    }
}

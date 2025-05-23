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
use App\Models\jasa_tambahan;
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
use App\Models\trx_jasa_penerbitan;
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
        // Add SuperAdminSeeder
        $this->call(SuperAdminSeeder::class);
        
        // kategori::factory(10)->create();
        // $this->call(KategoriSeeder::class);

        // // buku dijual
        // penulis::factory(20)->create();
        // buku_dijual::factory(50)->create();
        // testimoni_pembeli::factory(20)->create();
        // bukudijual_penulis_pivot::factory(50)->create();
        // keranjang::factory(20)->create();
        // buku_lunas_user::factory(20)->create();
        // storage_buku_dijual::factory(50)->create();
        // transaksi_penjualan_buku::factory(20)->create();
        // list_transaksi_buku::factory(30)->create();

        // // config web
        // konten_event::factory(10)->create();
        // $this->call(KontenEventSeeder::class);
        // $this->call(KontenFaqSeeder::class);
        // konten_faq::factory(10)->create();
        // $this->call(ConfigWebSeeder::class);
        // config_web::factory(10)->create();

        // // jasa tambahan
        // jasa_tambahan::factory(10)->create();

        // // paket penerbitan
        // paket_penerbitan::factory(10)->create();
        // jasa_paket_penerbitan::factory(20)->create();
        // buku_permohonan_terbit::factory(30)->create();
        // transaksi_paket_penerbitan::factory(30)->create();
        // trx_jasa_penerbitan::factory(30)->create();

        // // buku kolaborasi
        // buku_kolaborasi::factory(10)->create();
        // bab_buku_kolaborasi::factory(10)->create();
        // user_bab_buku_kolaborasi::factory(10)->create();
        // transaksi_kolaborasi_buku::factory(20)->create();

        // mou
        // DB::table('mou')->insert([
        //     'id' => \Ramsey\Uuid\Uuid::uuid4(),
        //     'nama' => "MOU Template Kolaborasi",
        //     'kategori' => "kolaborasi",
        //     'file_mou' => '/mou_file/contoh_mou_kolaborasi.pdf',
        //     'active_flag' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('mou')->insert([
        //     'id' => \Ramsey\Uuid\Uuid::uuid4(),
        //     'nama' => "MOU Template Penerbitan",
        //     'kategori' => "paket penerbitan",
        //     'file_mou' => '/mou_file/contoh_mou_penerbitan.pdf',
        //     'active_flag' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
    }
}

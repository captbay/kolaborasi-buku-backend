<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // make data
        $data = [
            [
                'nama' => 'Agama',
                'slug' => 'agama',
                'deskripsi' => 'Kategori buku agama',
            ],
            [
                'nama' => 'Alam',
                'slug' => 'alam',
                'deskripsi' => 'Kategori buku sejarah',
            ],
            [
                'nama' => 'Aliran & Gaya Bahasa',
                'slug' => 'aliran-gaya-bahasa',
                'deskripsi' => 'Kategori buku aliran & gaya bahasa',
            ],
            [
                'nama' => 'Arsitektur',
                'slug' => 'arsitektur',
                'deskripsi' => 'Kategori buku arsitektur',
            ],
            [
                'nama' => 'Barang Antik & Koleksi',
                'slug' => 'barang-antik-koleksi',
                'deskripsi' => 'Kategori buku barang antik & koleksi',
            ],
            [
                'nama' => 'Berkebun',
                'slug' => 'berkebun',
                'deskripsi' => 'Kategori buku berkebun',
            ],
            [
                'nama' => 'Biografi & Autobiografi',
                'slug' => 'biografi-autobiografi',
                'deskripsi' => 'Kategori buku biografi & autobiografi',
            ],
            [
                'nama' => 'Bisnis & Ekonomi',
                'slug' => 'bisnis-ekonomi',
                'deskripsi' => 'Kategori buku bisnis & ekonomi',
            ],
            [
                'nama' => 'Desain',
                'slug' => 'desain',
                'deskripsi' => 'Kategori buku desain',
            ],
            [
                'nama' => 'Fiksi Anak',
                'slug' => 'fiksi-anak',
                'deskripsi' => 'Kategori buku fiksi anak',
            ],
            [
                'nama' => 'Fiksi Dewasa',
                'slug' => 'fiksi-dewasa',
                'deskripsi' => 'Kategori buku fiksi dewasa',
            ],
            [
                'nama' => 'Filsafat',
                'slug' => 'filsafat',
                'deskripsi' => 'Kategori buku filsafat',
            ],
            [
                'nama' => 'Fotografi',
                'slug' => 'fotografi',
                'deskripsi' => 'Kategori buku fotografi',
            ],
            [
                'nama' => 'Game & Aktivitas',
                'slug' => 'game-aktivitas',
                'deskripsi' => 'Kategori buku game & aktivitas',
            ],
            [
                'nama' => 'Hukum',
                'slug' => 'hukum',
                'deskripsi' => 'Kategori buku hukum',
            ],
            [
                'nama' => 'Humor',
                'slug' => 'humor',
                'deskripsi' => 'Kategori buku humor',
            ],
            [
                'nama' => 'Ilmu Sosial',
                'slug' => 'ilmu-sosial',
                'deskripsi' => 'Kategori buku ilmu sosial',
            ],
            [
                'nama' => 'Kesehatan',
                'slug' => 'kesehatan',
                'deskripsi' => 'Kategori buku kesehatan',
            ],
            [
                'nama' => 'Komputer',
                'slug' => 'komputer',
                'deskripsi' => 'Kategori buku komputer',
            ],
            [
                'nama' => 'Matematika',
                'slug' => 'matematika',
                'deskripsi' => 'Kategori buku matematika',
            ],
            [
                'nama' => 'Musik',
                'slug' => 'musik',
                'deskripsi' => 'Kategori buku musik',
            ],
            [
                'nama' => 'Olahraga',
                'slug' => 'olahraga',
                'deskripsi' => 'Kategori buku olahraga',
            ],
            [
                'nama' => 'Pendidikan',
                'slug' => 'pendidikan',
                'deskripsi' => 'Kategori buku pendidikan',
            ],
            [
                'nama' => 'Psikologi',
                'slug' => 'psikologi',
                'deskripsi' => 'Kategori buku psikologi',
            ],
            [
                'nama' => 'Sains',
                'slug' => 'sains',
                'deskripsi' => 'Kategori buku sains',
            ],
            [
                'nama' => 'Sejarah',
                'slug' => 'sejarah',
                'deskripsi' => 'Kategori buku sejarah',
            ],
            [
                'nama' => 'Teknologi & Teknik',
                'slug' => 'teknologi-teknik',
                'deskripsi' => 'Kategori buku teknologi & teknik',
            ],
            [
                'nama' => 'Travel & Perjalanan',
                'slug' => 'travel-perjalanan',
                'deskripsi' => 'Kategori buku travel & perjalanan',
            ]
        ];

        foreach ($data as $d) {
            DB::table('kategori')->insert([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'nama' => $d['nama'],
                'slug' => $d['slug'],
                'deskripsi' => $d['deskripsi'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

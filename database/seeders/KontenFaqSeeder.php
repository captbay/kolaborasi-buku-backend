<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KontenFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // make data
        $data = [
            [
                'judul' => 'Proses Penerbitan Buku',
                'answer' => 'Dapatkan informasi lengkap mengenai proses penerbitan buku dari konsep hingga distribusi.'
            ],
            [
                'judul' => 'Kolaborasi Penulis',
                'answer' => 'Temukan manfaat dan tips sukses untuk kolaborasi antara penulis dalam proyek buku.'
            ],
            [
                'judul' => 'Panduan Membeli Buku',
                'answer' => 'Cari tahu cara terbaik untuk membeli buku secara online atau di toko buku lokal.'
            ],
            [
                'judul' => 'Penerbitan Buku Digital',
                'answer' => 'Pelajari tentang penerbitan buku dalam format digital, termasuk e-book dan audiobook.'
            ],
            [
                'judul' => 'Hak Cipta dan Lisensi',
                'answer' => 'Informasi mengenai hak cipta buku, lisensi, dan perlindungan legal bagi penulis.'
            ],
            [
                'judul' => 'Promosi dan Pemasaran Buku',
                'answer' => 'Tips untuk mempromosikan dan memasarkan buku Anda agar lebih dikenal di pasaran.'
            ],
            [
                'judul' => 'Penyuntingan dan Revisi',
                'answer' => 'Peran penting penyuntingan dan revisi dalam proses penulisan dan penerbitan buku.'
            ],
            [
                'judul' => 'Pengiriman dan Pengemasan',
                'answer' => 'Prosedur pengiriman dan pengemasan yang efektif untuk buku fisik kepada pembeli.'
            ],
            [
                'judul' => 'Kebijakan Pengembalian Buku',
                'answer' => 'Ketentuan dan prosedur pengembalian buku yang diterapkan oleh penerbit atau toko buku.'
            ],
            [
                'judul' => 'Konsultasi Penulisan',
                'answer' => 'Layanan konsultasi untuk penulis yang membutuhkan bantuan dalam menyelesaikan buku.'
            ]
        ];


        foreach ($data as $d) {
            DB::table('konten_faq')->insert([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'judul' => $d['judul'],
                'answer' => $d['answer'],
                'active_flag' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

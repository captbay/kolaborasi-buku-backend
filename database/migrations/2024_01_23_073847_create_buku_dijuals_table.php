<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buku_dijual', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('judul');
            $table->integer('harga');
            $table->date('tanggal_terbit');
            $table->string('cover_buku');
            $table->text('deskripsi');
            $table->integer('jumlah_halaman');
            $table->string('bahasa');
            $table->string('penerbit');
            $table->boolean('active_flag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_dijual');
    }
};

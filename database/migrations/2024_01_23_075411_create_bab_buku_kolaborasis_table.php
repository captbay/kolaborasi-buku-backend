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
        Schema::create('bab_buku_kolaborasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('buku_kolaborasi_id')->constrained('buku_kolaborasi')->cascadeOnDelete();
            $table->integer('no_bab');
            $table->string('judul');
            $table->integer('harga');
            $table->integer('durasi_pembuatan');
            $table->text('deskripsi');
            $table->integer('active_flag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bab_buku_kolaborasi');
    }
};

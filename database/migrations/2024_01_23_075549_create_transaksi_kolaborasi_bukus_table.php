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
        Schema::create('transaksi_kolaborasi_buku', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('bab_buku_kolaborasi_id')->constrained('bab_buku_kolaborasi')->cascadeOnDelete();
            $table->string('no_transaksi');
            $table->integer('total_harga');
            $table->enum('status', ['DONE', 'PROGRESS', 'FAILED', 'UPLOADED']);
            $table->dateTime('date_time_exp')->nullable();
            $table->string('foto_bukti_bayar')->nullable();
            $table->dateTime('date_time_lunas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kolaborasi_buku');
    }
};

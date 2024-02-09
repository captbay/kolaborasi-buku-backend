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
        Schema::create('transaksi_paket_penerbitan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('paket_penerbitan_id')->constrained('paket_penerbitan')->cascadeOnDelete();
            $table->foreignUuid('buku_permohonan_terbit_id')->constrained('buku_permohonan_terbit')->cascadeOnDelete();
            $table->string('no_transaksi');
            $table->string('total_harga');
            $table->enum('status', ['DONE', 'PROGRESS', 'FAILED', 'UPLOADED']);
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
        Schema::dropIfExists('transaksi_paket_penerbitan');
    }
};

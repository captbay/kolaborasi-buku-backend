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
        Schema::create('list_transaksi_buku', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaksi_penjualan_buku_id')->constrained('transaksi_penjualan_buku')->cascadeOnDelete();
            $table->foreignUuid('buku_dijual_id')->constrained('buku_dijual')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_transaksi_buku');
    }
};

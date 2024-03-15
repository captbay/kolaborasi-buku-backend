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
        Schema::create('trx_jasa_penerbitan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jasa_tambahan_id')->constrained('jasa_tambahan')->cascadeOnDelete();
            $table->foreignUuid('transaksi_paket_penerbitan_id')->constrained('transaksi_paket_penerbitan')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_jasa_penerbitan');
    }
};

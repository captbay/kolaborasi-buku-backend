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
        Schema::create('storage_buku_dijual', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('buku_dijual_id')->constrained('buku_dijual')->cascadeOnDelete();
            $table->enum('tipe', ['IMAGE', 'PDF']);
            $table->string('nama_file');
            $table->string('nama_generate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_buku_dijual');
    }
};

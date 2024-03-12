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
        Schema::create('jasa_paket_penerbitan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paket_penerbitan_id')->constrained('paket_penerbitan')->cascadeOnDelete();
            $table->string('nama');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jasa_paket_penerbitan');
    }
};

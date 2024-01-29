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
        Schema::create('bukudijual_penulis_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('buku_dijual_id')->constrained('buku_dijual')->cascadeOnDelete();
            $table->foreignUuid('penulis_id')->constrained('penulis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukudijual_penulis_pivot');
    }
};

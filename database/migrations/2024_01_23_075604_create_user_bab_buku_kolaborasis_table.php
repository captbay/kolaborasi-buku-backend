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
        Schema::create('user_bab_buku_kolaborasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('bab_buku_kolaborasi_id')->constrained('bab_buku_kolaborasi')->cascadeOnDelete();
            $table->enum('status', ['DONE', 'PROGRESS', 'REVISI', 'REJECTED']);
            $table->text('note')->nullable();
            $table->string('file_bab')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bab_buku_kolaborasi');
    }
};

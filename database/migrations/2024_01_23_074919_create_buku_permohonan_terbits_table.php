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
        Schema::create('buku_permohonan_terbit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->integer('persen_bagi_hasil');
            $table->enum('status', ['ACCEPTED', 'REVIEW', 'REVISI', 'REJECTED']);
            $table->string('cover_buku');
            $table->string('file_buku');
            $table->string('file_mou')->nullable();
            $table->boolean('dijual')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_permohonan_terbit');
    }
};

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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('no_telepon');
            $table->date("tgl_lahir");
            $table->string('gender');
            $table->string('alamat');
            $table->string('provinsi');
            $table->string('kecamatan');
            $table->string('kota');
            $table->integer('kode_pos');
            $table->string('foto_profil');
            $table->string('bio');
            $table->integer('kode_verif_email');
            $table->integer('status_verif_email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('file_cv');
            $table->string('file_ktp');
            $table->string('file_ttd');
            $table->enum('role', ['ADMIN', 'CUSTOMER', 'MEMBER'])->default('CUSTOMER');
            $table->integer('active_flag');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

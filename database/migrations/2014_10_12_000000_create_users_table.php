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
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('no_telepon');
            $table->date('tgl_lahir')->nullable();
            $table->string('gender')->nullable();
            $table->string('alamat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('foto_profil')->nullable();
            $table->string('bio')->nullable();
            $table->integer('status_verif_email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('file_cv')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_ttd')->nullable();
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

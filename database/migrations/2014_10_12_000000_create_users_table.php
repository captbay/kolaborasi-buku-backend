<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            $table->string('nama_lengkap')->virtualAs('concat(nama_depan, \' \', nama_belakang)');
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
            $table->boolean('status_verif_email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('file_cv')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_ttd')->nullable();
            $table->enum('role', ['ADMIN', 'CUSTOMER', 'MEMBER'])->default('CUSTOMER');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('users')->insert([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'nama_depan' => 'admin',
            'nama_belakang' => 'penerbitan',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'no_telepon' => '081234567890',
            'status_verif_email' => 1,
            'email_verified_at' => now(),
            'role' => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

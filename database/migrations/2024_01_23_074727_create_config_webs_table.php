<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('config_web', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key');
            $table->string('tipe');
            $table->string('value');
            $table->boolean('active_flag');
            $table->timestamps();
        });

        $data = [
            [
                'key' => 'no_rek',
                'tipe' => 'TEXT',
                'value' => 'NOMOR REKENING BANK',
            ],
            [
                'key' => 'bank_rek',
                'tipe' => 'TEXT',
                'value' => 'NAMA BANK PENERBITAN',
            ],
            [
                'key' => 'nama_rek',
                'tipe' => 'TEXT',
                'value' => 'NAMA REKENING BANK',
            ],
        ];

        foreach ($data as $d) {
            DB::table('config_web')->insert([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'key' => $d['key'],
                'tipe' => $d['tipe'],
                'value' => $d['value'],
                'active_flag' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_web');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rumpun_jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        // Seed the initial values (3 existing + 4 new) so FK migration can reference them
        DB::table('rumpun_jabatans')->insert([
            ['nama' => 'Imigrasi', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pemasyarakatan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Struktural', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'JFT', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'JFU', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'PPPK', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rumpun_jabatans');
    }
};

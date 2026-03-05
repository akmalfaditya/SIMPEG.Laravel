<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabel_gajis', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('golongan_ruang');
            $table->integer('masa_kerja_tahun');
            $table->decimal('gaji_pokok', 15, 2);
            $table->timestamps();

            $table->unique(['golongan_ruang', 'masa_kerja_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabel_gajis');
    }
};

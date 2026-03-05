<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->tinyInteger('jenis_jabatan');
            $table->integer('bup');
            $table->integer('eselon_level')->default(0);
            $table->integer('kelas_jabatan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('golongan_pangkats', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('golongan_ruang')->unique();
            $table->string('label', 10);
            $table->string('pangkat', 50);
            $table->string('golongan_group', 5);
            $table->string('min_pendidikan', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('golongan_pangkats');
    }
};

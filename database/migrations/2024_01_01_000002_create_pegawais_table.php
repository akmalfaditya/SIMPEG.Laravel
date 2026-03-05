<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->tinyInteger('jenis_kelamin');
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->date('tmt_cpns');
            $table->date('tmt_pns')->nullable();
            $table->string('foto_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            // ASN Standard Fields
            $table->tinyInteger('agama')->default(1);
            $table->tinyInteger('status_pernikahan')->default(1);
            $table->tinyInteger('golongan_darah')->default(1);
            $table->string('npwp')->nullable();
            $table->string('no_karpeg')->nullable();
            $table->string('no_taspen')->nullable();
            $table->string('unit_kerja')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};

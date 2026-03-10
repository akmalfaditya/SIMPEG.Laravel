<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create 8 new master tables
        Schema::create('tipe_pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::create('status_kepegawaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::create('bagians', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::create('unit_kerjas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::create('jenis_kelamins', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::create('agamas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::create('status_pernikahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::create('golongan_darahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        // 2. Drop old columns from pegawais
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'agama',
                'status_pernikahan',
                'golongan_darah',
                'tipe_pegawai',
                'status_kepegawaian',
                'bagian',
                'unit_kerja',
            ]);
        });

        // 3. Add foreign key columns to pegawais
        Schema::table('pegawais', function (Blueprint $table) {
            $table->foreignId('jenis_kelamin_id')->after('tanggal_lahir')->constrained('jenis_kelamins');
            $table->foreignId('agama_id')->after('jenis_kelamin_id')->constrained('agamas');
            $table->foreignId('status_pernikahan_id')->after('agama_id')->constrained('status_pernikahans');
            $table->foreignId('golongan_darah_id')->after('status_pernikahan_id')->constrained('golongan_darahs');
            $table->foreignId('tipe_pegawai_id')->after('tmt_pns')->constrained('tipe_pegawais');
            $table->foreignId('status_kepegawaian_id')->after('tipe_pegawai_id')->constrained('status_kepegawaans');
            $table->foreignId('bagian_id')->nullable()->after('status_kepegawaian_id')->constrained('bagians');
            $table->foreignId('unit_kerja_id')->nullable()->after('bagian_id')->constrained('unit_kerjas');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jenis_kelamin_id');
            $table->dropConstrainedForeignId('agama_id');
            $table->dropConstrainedForeignId('status_pernikahan_id');
            $table->dropConstrainedForeignId('golongan_darah_id');
            $table->dropConstrainedForeignId('tipe_pegawai_id');
            $table->dropConstrainedForeignId('status_kepegawaian_id');
            $table->dropConstrainedForeignId('bagian_id');
            $table->dropConstrainedForeignId('unit_kerja_id');
        });

        Schema::table('pegawais', function (Blueprint $table) {
            $table->tinyInteger('jenis_kelamin')->default(1);
            $table->tinyInteger('agama')->default(1);
            $table->tinyInteger('status_pernikahan')->default(1);
            $table->tinyInteger('golongan_darah')->default(1);
            $table->string('tipe_pegawai')->default('PNS');
            $table->string('status_kepegawaian')->default('Aktif');
            $table->string('bagian')->nullable();
            $table->string('unit_kerja')->nullable();
        });

        Schema::dropIfExists('golongan_darahs');
        Schema::dropIfExists('status_pernikahans');
        Schema::dropIfExists('agamas');
        Schema::dropIfExists('jenis_kelamins');
        Schema::dropIfExists('unit_kerjas');
        Schema::dropIfExists('bagians');
        Schema::dropIfExists('status_kepegawaans');
        Schema::dropIfExists('tipe_pegawais');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('gelar_depan')->nullable()->after('nip');
            $table->string('gelar_belakang')->nullable()->after('nama_lengkap');
            $table->string('bagian')->nullable()->after('unit_kerja');
            $table->string('tipe_pegawai')->default('PNS')->after('bagian');
            $table->string('status_kepegawaian')->default('Aktif')->after('tipe_pegawai');
        });

        // Set unit_kerja default for existing records
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('unit_kerja')->default('Kanim Jakut')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['gelar_depan', 'gelar_belakang', 'bagian', 'tipe_pegawai', 'status_kepegawaian']);
        });
    }
};

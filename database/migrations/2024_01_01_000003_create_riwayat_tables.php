<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pangkats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->tinyInteger('golongan_ruang');
            $table->string('nomor_sk')->nullable();
            $table->date('tmt_pangkat');
            $table->date('tanggal_sk');
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->string('nomor_sk')->nullable();
            $table->date('tmt_jabatan');
            $table->date('tanggal_sk');
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_kgbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nomor_sk')->nullable();
            $table->date('tmt_kgb');
            $table->decimal('gaji_lama', 15, 2);
            $table->decimal('gaji_baru', 15, 2);
            $table->integer('masa_kerja_golongan_tahun')->default(0);
            $table->integer('masa_kerja_golongan_bulan')->default(0);
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_hukuman_disiplins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->tinyInteger('tingkat_hukuman');
            $table->string('jenis_hukuman');
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tmt_hukuman');
            $table->date('tmt_selesai_hukuman')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('tingkat_pendidikan');
            $table->string('institusi');
            $table->string('jurusan');
            $table->integer('tahun_lulus');
            $table->string('no_ijazah')->nullable();
            $table->date('tanggal_ijazah')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_latihan_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nama_latihan');
            $table->integer('tahun_pelaksanaan');
            $table->integer('jumlah_jam')->default(0);
            $table->string('penyelenggara')->nullable();
            $table->string('tempat_pelaksanaan')->nullable();
            $table->string('no_sertifikat')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_penghargaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nama_penghargaan');
            $table->integer('tahun');
            $table->integer('milestone');
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('google_drive_link')->nullable();
            $table->timestamps();
        });

        Schema::create('penilaian_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->integer('tahun');
            $table->string('nilai_skp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kinerjas');
        Schema::dropIfExists('riwayat_penghargaans');
        Schema::dropIfExists('riwayat_latihan_jabatans');
        Schema::dropIfExists('riwayat_pendidikans');
        Schema::dropIfExists('riwayat_hukuman_disiplins');
        Schema::dropIfExists('riwayat_kgbs');
        Schema::dropIfExists('riwayat_jabatans');
        Schema::dropIfExists('riwayat_pangkats');
    }
};

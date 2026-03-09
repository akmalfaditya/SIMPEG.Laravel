<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- riwayat_pangkats ---
        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->foreignId('golongan_id')->nullable()->after('pegawai_id')->constrained('golongan_pangkats')->cascadeOnDelete();
        });

        // Migrate data: map golongan_ruang tinyint → golongan_pangkats.id
        DB::table('riwayat_pangkats')->whereNotNull('golongan_ruang')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $gp = DB::table('golongan_pangkats')->where('golongan_ruang', $row->golongan_ruang)->first();
                if ($gp) {
                    DB::table('riwayat_pangkats')->where('id', $row->id)->update(['golongan_id' => $gp->id]);
                }
            }
        });

        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->dropColumn('golongan_ruang');
        });

        // --- tabel_gajis ---
        // Drop the old unique constraint first
        Schema::table('tabel_gajis', function (Blueprint $table) {
            $table->dropUnique(['golongan_ruang', 'masa_kerja_tahun']);
        });

        Schema::table('tabel_gajis', function (Blueprint $table) {
            $table->foreignId('golongan_id')->nullable()->after('id')->constrained('golongan_pangkats')->cascadeOnDelete();
        });

        // Migrate data
        DB::table('tabel_gajis')->whereNotNull('golongan_ruang')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $gp = DB::table('golongan_pangkats')->where('golongan_ruang', $row->golongan_ruang)->first();
                if ($gp) {
                    DB::table('tabel_gajis')->where('id', $row->id)->update(['golongan_id' => $gp->id]);
                }
            }
        });

        Schema::table('tabel_gajis', function (Blueprint $table) {
            $table->dropColumn('golongan_ruang');
            $table->unique(['golongan_id', 'masa_kerja_tahun']);
        });
    }

    public function down(): void
    {
        // --- tabel_gajis ---
        Schema::table('tabel_gajis', function (Blueprint $table) {
            $table->dropUnique(['golongan_id', 'masa_kerja_tahun']);
        });

        Schema::table('tabel_gajis', function (Blueprint $table) {
            $table->tinyInteger('golongan_ruang')->nullable()->after('id');
        });

        DB::table('tabel_gajis')->whereNotNull('golongan_id')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $gp = DB::table('golongan_pangkats')->where('id', $row->golongan_id)->first();
                if ($gp) {
                    DB::table('tabel_gajis')->where('id', $row->id)->update(['golongan_ruang' => $gp->golongan_ruang]);
                }
            }
        });

        Schema::table('tabel_gajis', function (Blueprint $table) {
            $table->dropForeign(['golongan_id']);
            $table->dropColumn('golongan_id');
            $table->unique(['golongan_ruang', 'masa_kerja_tahun']);
        });

        // --- riwayat_pangkats ---
        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->tinyInteger('golongan_ruang')->nullable()->after('pegawai_id');
        });

        DB::table('riwayat_pangkats')->whereNotNull('golongan_id')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $gp = DB::table('golongan_pangkats')->where('id', $row->golongan_id)->first();
                if ($gp) {
                    DB::table('riwayat_pangkats')->where('id', $row->id)->update(['golongan_ruang' => $gp->golongan_ruang]);
                }
            }
        });

        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->dropForeign(['golongan_id']);
            $table->dropColumn('golongan_id');
        });
    }
};

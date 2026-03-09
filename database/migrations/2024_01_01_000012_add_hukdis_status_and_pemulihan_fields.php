<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_hukuman_disiplins', function (Blueprint $table) {
            $table->string('status')->default('aktif')->after('google_drive_link');
            $table->string('nomor_sk_pemulihan')->nullable()->after('status');
            $table->date('tanggal_pemulihan')->nullable()->after('nomor_sk_pemulihan');
            $table->string('file_sk_pemulihan_path')->nullable()->after('tanggal_pemulihan');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_hukuman_disiplins', function (Blueprint $table) {
            $table->dropColumn(['status', 'nomor_sk_pemulihan', 'tanggal_pemulihan', 'file_sk_pemulihan_path']);
        });
    }
};

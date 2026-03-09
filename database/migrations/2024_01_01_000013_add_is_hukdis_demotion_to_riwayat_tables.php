<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->boolean('is_hukdis_demotion')->default(false)->after('google_drive_link');
        });

        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->boolean('is_hukdis_demotion')->default(false)->after('google_drive_link');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_pangkats', function (Blueprint $table) {
            $table->dropColumn('is_hukdis_demotion');
        });

        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->dropColumn('is_hukdis_demotion');
        });
    }
};

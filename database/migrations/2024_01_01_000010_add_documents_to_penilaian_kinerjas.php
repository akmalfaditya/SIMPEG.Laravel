<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian_kinerjas', function (Blueprint $table) {
            $table->string('file_pdf_path')->nullable()->after('nilai_skp');
            $table->string('google_drive_link')->nullable()->after('file_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian_kinerjas', function (Blueprint $table) {
            $table->dropColumn(['file_pdf_path', 'google_drive_link']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('file_sk_pensiun_path')->nullable()->after('catatan_pensiun');
            $table->string('link_sk_pensiun_gdrive')->nullable()->after('file_sk_pensiun_path');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['file_sk_pensiun_path', 'link_sk_pensiun_gdrive']);
        });
    }
};

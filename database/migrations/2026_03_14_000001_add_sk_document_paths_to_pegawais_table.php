<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('sk_cpns_path')->nullable()->after('foto_path');
            $table->string('sk_pns_path')->nullable()->after('sk_cpns_path');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['sk_cpns_path', 'sk_pns_path']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('sk_pensiun_nomor')->nullable()->after('no_taspen');
            $table->date('sk_pensiun_tanggal')->nullable()->after('sk_pensiun_nomor');
            $table->date('tmt_pensiun')->nullable()->after('sk_pensiun_tanggal');
            $table->text('catatan_pensiun')->nullable()->after('tmt_pensiun');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['sk_pensiun_nomor', 'sk_pensiun_tanggal', 'tmt_pensiun', 'catatan_pensiun']);
        });
    }
};

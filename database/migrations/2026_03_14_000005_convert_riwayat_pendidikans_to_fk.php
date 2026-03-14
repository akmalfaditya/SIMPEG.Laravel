<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Seed Master Pendidikan first so we have data to map to
        $pendidikanData = [
            ['nama' => 'SMP', 'bobot' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'SMA', 'bobot' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'D3',  'bobot' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'S1',  'bobot' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'S2',  'bobot' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'S3',  'bobot' => 6, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('master_pendidikans')->insert($pendidikanData);

        // Map existing string values to new IDs
        $pendidikanMap = DB::table('master_pendidikans')->pluck('id', 'nama');
        
        $oldToNew = [
            'SD' => $pendidikanMap['SMP'], // Map SD to SMP as it's the lowest available
            'SMP' => $pendidikanMap['SMP'],
            'SMA' => $pendidikanMap['SMA'],
            'SMK' => $pendidikanMap['SMA'],
            'SMU' => $pendidikanMap['SMA'],
            'D1' => $pendidikanMap['SMA'], // Map D1 to SMA
            'D2' => $pendidikanMap['SMA'], // Map D2 to SMA
            'D3' => $pendidikanMap['D3'],
            'D4' => $pendidikanMap['S1'], // D4 is equivalent to S1
            'S1' => $pendidikanMap['S1'],
            'S2' => $pendidikanMap['S2'],
            'S3' => $pendidikanMap['S3'],
        ];

        // 2. Add foreign key column
        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->foreignId('pendidikan_id')
                  ->nullable()
                  ->after('pegawai_id')
                  ->constrained('master_pendidikans')
                  ->onDelete('restrict');
        });

        // 3. Migrate data
        foreach ($oldToNew as $oldStringVal => $newId) {
            DB::table('riwayat_pendidikans')
                ->where('tingkat_pendidikan', $oldStringVal)
                ->update(['pendidikan_id' => $newId]);
        }

        // Deal with any remaining unmapped values by setting them to SMA as a safe default
        DB::table('riwayat_pendidikans')
            ->whereNull('pendidikan_id')
            ->update(['pendidikan_id' => $pendidikanMap['SMA']]);

        // 4. Drop the old column
        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->dropColumn('tingkat_pendidikan');
        });
    }

    public function down(): void
    {
        // Reverse process
        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->string('tingkat_pendidikan')->nullable()->after('pendidikan_id');
        });

        $pendidikanMap = DB::table('master_pendidikans')->pluck('nama', 'id');
        
        foreach ($pendidikanMap as $id => $nama) {
            DB::table('riwayat_pendidikans')
                ->where('pendidikan_id', $id)
                ->update(['tingkat_pendidikan' => $nama]);
        }

        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pendidikan_id');
        });
        
        // Clean up seeded data in down
        DB::table('master_pendidikans')->truncate();
    }
};

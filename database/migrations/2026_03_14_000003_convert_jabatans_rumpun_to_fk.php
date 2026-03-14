<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Build mapping: old integer values → new rumpun_jabatan FK IDs
        $rumpunMap = DB::table('rumpun_jabatans')->pluck('id', 'nama')->toArray();
        $oldToNew = [
            1 => $rumpunMap['Imigrasi'],
            2 => $rumpunMap['Pemasyarakatan'],
            3 => $rumpunMap['Struktural'],
        ];

        // Step 1: Add the FK column (nullable initially for safe migration)
        Schema::table('jabatans', function (Blueprint $table) {
            $table->foreignId('rumpun_jabatan_id')
                ->nullable()
                ->after('kelas_jabatan')
                ->constrained('rumpun_jabatans')
                ->onDelete('restrict');
        });

        // Step 2: Populate FK from old integer column
        foreach ($oldToNew as $oldValue => $newId) {
            DB::table('jabatans')
                ->where('rumpun', $oldValue)
                ->update(['rumpun_jabatan_id' => $newId]);
        }

        // Step 3: Drop old rumpun column
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropColumn('rumpun');
        });
    }

    public function down(): void
    {
        // Reverse: re-add rumpun column, populate from FK, drop FK
        $rumpunMap = DB::table('rumpun_jabatans')->pluck('nama', 'id')->toArray();
        $nameToOld = [
            'Imigrasi' => 1,
            'Pemasyarakatan' => 2,
            'Struktural' => 3,
        ];

        Schema::table('jabatans', function (Blueprint $table) {
            $table->tinyInteger('rumpun')->default(3)->after('kelas_jabatan');
        });

        foreach ($rumpunMap as $id => $nama) {
            if (isset($nameToOld[$nama])) {
                DB::table('jabatans')
                    ->where('rumpun_jabatan_id', $id)
                    ->update(['rumpun' => $nameToOld[$nama]]);
            }
        }

        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rumpun_jabatan_id');
        });
    }
};

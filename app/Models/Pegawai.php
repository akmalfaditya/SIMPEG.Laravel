<?php

namespace App\Models;

use App\Enums\Agama;
use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusPernikahan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nip', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'alamat', 'no_telepon', 'email',
        'tmt_cpns', 'tmt_pns', 'foto_path', 'is_active', 'gaji_pokok',
        'agama', 'status_pernikahan', 'golongan_darah',
        'npwp', 'no_karpeg', 'no_taspen', 'unit_kerja',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
        'jenis_kelamin' => JenisKelamin::class,
        'agama' => Agama::class,
        'status_pernikahan' => StatusPernikahan::class,
        'golongan_darah' => GolonganDarah::class,
        'gaji_pokok' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function riwayatPangkat(): HasMany
    {
        return $this->hasMany(RiwayatPangkat::class);
    }

    public function riwayatJabatan(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function riwayatKgb(): HasMany
    {
        return $this->hasMany(RiwayatKgb::class);
    }

    public function riwayatHukumanDisiplin(): HasMany
    {
        return $this->hasMany(RiwayatHukumanDisiplin::class);
    }

    public function riwayatPendidikan(): HasMany
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }

    public function riwayatLatihanJabatan(): HasMany
    {
        return $this->hasMany(RiwayatLatihanJabatan::class);
    }

    public function riwayatPenghargaan(): HasMany
    {
        return $this->hasMany(RiwayatPenghargaan::class);
    }

    public function penilaianKinerja(): HasMany
    {
        return $this->hasMany(PenilaianKinerja::class);
    }

    public function getMasaKerjaAttribute(): string
    {
        $tmtCpns = $this->tmt_cpns;
        $today = now();
        $totalMonths = (($today->year - $tmtCpns->year) * 12) + $today->month - $tmtCpns->month;
        if ($today->day < $tmtCpns->day) {
            $totalMonths--;
        }
        $years = intdiv($totalMonths, 12);
        $months = $totalMonths % 12;
        return "{$years} Tahun {$months} Bulan";
    }

    public function getPangkatTerakhirAttribute(): ?string
    {
        $pangkat = $this->riwayatPangkat->sortByDesc('tmt_pangkat')->first();
        return $pangkat?->golongan_ruang?->label();
    }

    public function getJabatanTerakhirAttribute(): ?string
    {
        $jabatan = $this->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
        return $jabatan?->jabatan?->nama_jabatan;
    }
}

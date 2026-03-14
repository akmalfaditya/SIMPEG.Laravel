<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pegawai extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah data pegawai #{$this->id} atas nama {$this->nama_lengkap}",
                'updated' => "Mengubah data pegawai #{$this->id} atas nama {$this->nama_lengkap}",
                'deleted' => "Menghapus data pegawai #{$this->id} atas nama {$this->nama_lengkap}",
                default   => "{$eventName} data pegawai #{$this->id} atas nama {$this->nama_lengkap}",
            });
    }

    protected $fillable = [
        'nip',
        'gelar_depan',
        'nama_lengkap',
        'gelar_belakang',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin_id',
        'agama_id',
        'status_pernikahan_id',
        'golongan_darah_id',
        'alamat',
        'no_telepon',
        'email',
        'tmt_cpns',
        'tmt_pns',
        'tipe_pegawai_id',
        'status_kepegawaian_id',
        'bagian_id',
        'unit_kerja_id',
        'foto_path',
        'sk_cpns_path',
        'sk_pns_path',
        'is_active',
        'gaji_pokok',
        'npwp',
        'no_karpeg',
        'no_taspen',
        'sk_pensiun_nomor',
        'sk_pensiun_tanggal',
        'tmt_pensiun',
        'catatan_pensiun',
        'file_sk_pensiun_path',
        'link_sk_pensiun_gdrive',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
        'gaji_pokok' => 'decimal:2',
        'is_active' => 'boolean',
        'sk_pensiun_tanggal' => 'date',
        'tmt_pensiun' => 'date',
    ];

    // BelongsTo relationships for master data
    public function jenisKelamin(): BelongsTo
    {
        return $this->belongsTo(JenisKelaminMaster::class, 'jenis_kelamin_id');
    }

    public function agama(): BelongsTo
    {
        return $this->belongsTo(AgamaMaster::class, 'agama_id');
    }

    public function statusPernikahan(): BelongsTo
    {
        return $this->belongsTo(StatusPernikahanMaster::class, 'status_pernikahan_id');
    }

    public function golonganDarah(): BelongsTo
    {
        return $this->belongsTo(GolonganDarahMaster::class, 'golongan_darah_id');
    }

    public function tipePegawai(): BelongsTo
    {
        return $this->belongsTo(TipePegawai::class);
    }

    public function statusKepegawaian(): BelongsTo
    {
        return $this->belongsTo(StatusKepegawaian::class);
    }

    public function bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

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
        return $pangkat?->golongan?->label;
    }

    public function getJabatanTerakhirAttribute(): ?string
    {
        $jabatan = $this->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
        return $jabatan?->jabatan?->nama_jabatan;
    }

    public function getHasActiveHukdisAttribute(): bool
    {
        return $this->riwayatHukumanDisiplin
            ->filter(fn($h) => $h->isAktif())
            ->isNotEmpty();
    }

    public function getActiveHukdisAttribute()
    {
        return $this->riwayatHukumanDisiplin
            ->filter(fn($h) => $h->isAktif());
    }
}

<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\StatusKepegawaian;
use App\Models\TabelGaji;
use App\DTOs\PegawaiDTO;
use Illuminate\Support\Facades\DB;

class PegawaiService
{
    private const EAGER_LOADS = ['riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb', 'riwayatHukumanDisiplin'];

    public function __construct(private DocumentUploadService $documentService) {}

    public function getAll()
    {
        return Pegawai::with(self::EAGER_LOADS)
            ->where('is_active', true)
            ->get();
    }

    public function getByStatus(string $status)
    {
        $query = Pegawai::with(self::EAGER_LOADS);

        return match ($status) {
            'aktif' => $query->where('is_active', true)->get(),
            'tidak-aktif' => $query->where('is_active', false)
                ->whereNull('tmt_pensiun')
                ->withTrashed()
                ->get(),
            'pensiun' => $query->where('is_active', false)
                ->whereNotNull('tmt_pensiun')
                ->withTrashed()
                ->get(),
            default => $query->where('is_active', true)->get(),
        };
    }

    public function getPaginatedByStatus(string $status, int $perPage = 10, ?string $search = null)
    {
        $query = Pegawai::with(self::EAGER_LOADS);

        $query = match ($status) {
            'aktif' => $query->where('is_active', true),
            'tidak-aktif' => $query->where('is_active', false)->whereNull('tmt_pensiun')->withTrashed(),
            'pensiun' => $query->where('is_active', false)->whereNotNull('tmt_pensiun')->withTrashed(),
            default => $query->where('is_active', true),
        };

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhereHas('unitKerja', fn ($r) => $r->where('nama', 'like', "%{$search}%"));
            });
        }

        return $query->orderBy('nama_lengkap')->paginate($perPage);
    }

    public function searchByStatus(string $keyword, string $status)
    {
        $query = Pegawai::with(self::EAGER_LOADS);

        $query = match ($status) {
            'aktif' => $query->where('is_active', true),
            'tidak-aktif' => $query->where('is_active', false)->whereNull('tmt_pensiun')->withTrashed(),
            'pensiun' => $query->where('is_active', false)->whereNotNull('tmt_pensiun')->withTrashed(),
            default => $query->where('is_active', true),
        };

        return $query->where(function ($q) use ($keyword) {
            $q->where('nip', 'like', "%{$keyword}%")
              ->orWhere('nama_lengkap', 'like', "%{$keyword}%")
              ->orWhereHas('unitKerja', fn ($r) => $r->where('nama', 'like', "%{$keyword}%"));
        })->get();
    }

    public function getById(int $id)
    {
        return Pegawai::with([
            'riwayatPangkat', 'riwayatJabatan.jabatan', 'riwayatKgb',
            'riwayatHukumanDisiplin', 'riwayatPendidikan',
            'riwayatLatihanJabatan', 'penilaianKinerja',
        ])->findOrFail($id);
    }

    public function search(string $keyword)
    {
        return Pegawai::with(self::EAGER_LOADS)
            ->where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('nip', 'like', "%{$keyword}%")
                  ->orWhere('nama_lengkap', 'like', "%{$keyword}%")
                  ->orWhereHas('unitKerja', fn ($r) => $r->where('nama', 'like', "%{$keyword}%"));
            })
            ->get();
    }

    public function reactivate(Pegawai $pegawai): void
    {
        DB::transaction(function () use ($pegawai) {
            $aktifStatusId = StatusKepegawaian::where('nama', 'Aktif')->value('id');

            if ($pegawai->trashed()) {
                $pegawai->restore();
            }

            $pegawai->update([
                'is_active' => true,
                'status_kepegawaian_id' => $aktifStatusId,
            ]);
        });
    }

    public function cancelPensiun(Pegawai $pegawai): void
    {
        DB::transaction(function () use ($pegawai) {
            $aktifStatusId = StatusKepegawaian::where('nama', 'Aktif')->value('id');

            if ($pegawai->trashed()) {
                $pegawai->restore();
            }

            $this->documentService->delete($pegawai->file_sk_pensiun_path);

            $pegawai->update([
                'is_active' => true,
                'status_kepegawaian_id' => $aktifStatusId,
                'sk_pensiun_nomor' => null,
                'sk_pensiun_tanggal' => null,
                'tmt_pensiun' => null,
                'catatan_pensiun' => null,
                'file_sk_pensiun_path' => null,
                'link_sk_pensiun_gdrive' => null,
            ]);
        });
    }

    /**
     * One-Stop Creation Flow:
     * Creates pegawai + auto-generates initial RiwayatPangkat & RiwayatJabatan
     * and looks up starting salary from TabelGaji.
     */
    public function create(PegawaiDTO $dto, int $golonganId, int $jabatanId): Pegawai
    {
        return DB::transaction(function () use ($dto, $golonganId, $jabatanId) {
            // 1. Lookup starting salary (masa_kerja = 0)
            $gajiPokok = TabelGaji::where('golongan_id', $golonganId)
                ->where('masa_kerja_tahun', 0)
                ->value('gaji_pokok') ?? 0;

            // 2. Create pegawai with auto-calculated gaji
            $data = $dto->toArray();
            $data['gaji_pokok'] = $gajiPokok;
            $pegawai = Pegawai::create($data);

            // 3. Auto-create first RiwayatPangkat
            $pegawai->riwayatPangkat()->create([
                'golongan_id' => $golonganId,
                'tmt_pangkat'  => $pegawai->tmt_cpns,
                'tanggal_sk'   => $pegawai->tmt_cpns,
                'nomor_sk'     => 'SK-CPNS/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            // 4. Auto-create first RiwayatJabatan
            $pegawai->riwayatJabatan()->create([
                'jabatan_id'  => $jabatanId,
                'tmt_jabatan' => $pegawai->tmt_cpns,
                'tanggal_sk'  => $pegawai->tmt_cpns,
                'nomor_sk'    => 'SK-JAB/' . $pegawai->tmt_cpns->year . '/AUTO',
            ]);

            return $pegawai;
        });
    }

    public function update(Pegawai $pegawai, PegawaiDTO $dto): bool
    {
        return DB::transaction(function() use ($pegawai, $dto) {
            return $pegawai->update($dto->toArray());
        });
    }

    public function delete(Pegawai $pegawai): bool
    {
        return DB::transaction(function() use ($pegawai) {
            $pegawai->is_active = false;
            $pegawai->save();
            return $pegawai->delete();
        });
    }
}

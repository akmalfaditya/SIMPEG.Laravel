<?php

namespace App\DTOs;

class PegawaiDTO
{
    public function __construct(
        public readonly string $nip,
        public readonly ?string $gelarDepan,
        public readonly string $namaLengkap,
        public readonly ?string $gelarBelakang,
        public readonly ?string $tempatLahir,
        public readonly string $tanggalLahir,
        public readonly int $jenisKelaminId,
        public readonly int $agamaId,
        public readonly int $statusPernikahanId,
        public readonly int $golonganDarahId,
        public readonly ?string $alamat,
        public readonly ?string $noTelepon,
        public readonly ?string $email,
        public readonly string $tmtCpns,
        public readonly ?string $tmtPns,
        public readonly float $gajiPokok,
        public readonly int $tipePegawaiId,
        public readonly int $statusKepegawaianId,
        public readonly ?int $bagianId,
        public readonly ?int $unitKerjaId,
        public readonly ?string $npwp,
        public readonly ?string $noKarpeg,
        public readonly ?string $noTaspen,
        public readonly ?string $skCpnsPath = null,
        public readonly ?string $skPnsPath = null,
        public readonly bool $isActive = true,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            nip: $validated['nip'],
            gelarDepan: $validated['gelar_depan'] ?? null,
            namaLengkap: $validated['nama_lengkap'],
            gelarBelakang: $validated['gelar_belakang'] ?? null,
            tempatLahir: $validated['tempat_lahir'] ?? null,
            tanggalLahir: $validated['tanggal_lahir'],
            jenisKelaminId: (int) $validated['jenis_kelamin_id'],
            agamaId: (int) $validated['agama_id'],
            statusPernikahanId: (int) $validated['status_pernikahan_id'],
            golonganDarahId: (int) $validated['golongan_darah_id'],
            alamat: $validated['alamat'] ?? null,
            noTelepon: $validated['no_telepon'] ?? null,
            email: $validated['email'] ?? null,
            tmtCpns: $validated['tmt_cpns'],
            tmtPns: $validated['tmt_pns'] ?? null,
            gajiPokok: (float) ($validated['gaji_pokok'] ?? 0),
            tipePegawaiId: (int) $validated['tipe_pegawai_id'],
            statusKepegawaianId: (int) $validated['status_kepegawaian_id'],
            bagianId: isset($validated['bagian_id']) ? (int) $validated['bagian_id'] : null,
            unitKerjaId: isset($validated['unit_kerja_id']) ? (int) $validated['unit_kerja_id'] : null,
            npwp: $validated['npwp'] ?? null,
            noKarpeg: $validated['no_karpeg'] ?? null,
            noTaspen: $validated['no_taspen'] ?? null,
            skCpnsPath: $validated['sk_cpns_path'] ?? null,
            skPnsPath: $validated['sk_pns_path'] ?? null,
            isActive: $validated['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'nip' => $this->nip,
            'gelar_depan' => $this->gelarDepan,
            'nama_lengkap' => $this->namaLengkap,
            'gelar_belakang' => $this->gelarBelakang,
            'tempat_lahir' => $this->tempatLahir,
            'tanggal_lahir' => $this->tanggalLahir,
            'jenis_kelamin_id' => $this->jenisKelaminId,
            'agama_id' => $this->agamaId,
            'status_pernikahan_id' => $this->statusPernikahanId,
            'golongan_darah_id' => $this->golonganDarahId,
            'alamat' => $this->alamat,
            'no_telepon' => $this->noTelepon,
            'email' => $this->email,
            'tmt_cpns' => $this->tmtCpns,
            'tmt_pns' => $this->tmtPns,
            'gaji_pokok' => $this->gajiPokok,
            'tipe_pegawai_id' => $this->tipePegawaiId,
            'status_kepegawaian_id' => $this->statusKepegawaianId,
            'bagian_id' => $this->bagianId,
            'unit_kerja_id' => $this->unitKerjaId,
            'npwp' => $this->npwp,
            'no_karpeg' => $this->noKarpeg,
            'no_taspen' => $this->noTaspen,
            'sk_cpns_path' => $this->skCpnsPath,
            'sk_pns_path' => $this->skPnsPath,
            'is_active' => $this->isActive,
        ];
    }
}

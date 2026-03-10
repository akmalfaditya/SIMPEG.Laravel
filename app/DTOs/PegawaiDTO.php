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
        public readonly int $jenisKelamin,
        public readonly ?string $alamat,
        public readonly ?string $noTelepon,
        public readonly ?string $email,
        public readonly string $tmtCpns,
        public readonly ?string $tmtPns,
        public readonly float $gajiPokok,
        public readonly int $agama,
        public readonly int $statusPernikahan,
        public readonly int $golonganDarah,
        public readonly ?string $npwp,
        public readonly ?string $noKarpeg,
        public readonly ?string $noTaspen,
        public readonly ?string $unitKerja,
        public readonly ?string $bagian,
        public readonly string $tipePegawai,
        public readonly string $statusKepegawaian,
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
            jenisKelamin: (int) $validated['jenis_kelamin'],
            alamat: $validated['alamat'] ?? null,
            noTelepon: $validated['no_telepon'] ?? null,
            email: $validated['email'] ?? null,
            tmtCpns: $validated['tmt_cpns'],
            tmtPns: $validated['tmt_pns'] ?? null,
            gajiPokok: (float) ($validated['gaji_pokok'] ?? 0),
            agama: (int) $validated['agama'],
            statusPernikahan: (int) $validated['status_pernikahan'],
            golonganDarah: (int) $validated['golongan_darah'],
            npwp: $validated['npwp'] ?? null,
            noKarpeg: $validated['no_karpeg'] ?? null,
            noTaspen: $validated['no_taspen'] ?? null,
            unitKerja: $validated['unit_kerja'] ?? 'Kanim Jakut',
            bagian: $validated['bagian'] ?? null,
            tipePegawai: $validated['tipe_pegawai'] ?? 'PNS',
            statusKepegawaian: $validated['status_kepegawaian'] ?? 'Aktif',
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
            'jenis_kelamin' => $this->jenisKelamin,
            'alamat' => $this->alamat,
            'no_telepon' => $this->noTelepon,
            'email' => $this->email,
            'tmt_cpns' => $this->tmtCpns,
            'tmt_pns' => $this->tmtPns,
            'gaji_pokok' => $this->gajiPokok,
            'agama' => $this->agama,
            'status_pernikahan' => $this->statusPernikahan,
            'golongan_darah' => $this->golonganDarah,
            'npwp' => $this->npwp,
            'no_karpeg' => $this->noKarpeg,
            'no_taspen' => $this->noTaspen,
            'unit_kerja' => $this->unitKerja,
            'bagian' => $this->bagian,
            'tipe_pegawai' => $this->tipePegawai,
            'status_kepegawaian' => $this->statusKepegawaian,
            'is_active' => $this->isActive,
        ];
    }
}

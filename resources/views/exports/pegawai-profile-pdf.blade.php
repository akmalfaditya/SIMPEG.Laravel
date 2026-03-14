<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profil Pegawai - {{ $pegawai->nama_lengkap }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #1e293b; margin: 20px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 2px; }
        .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 4px; }
        .date { text-align: center; font-size: 9px; color: #94a3b8; margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: bold; margin: 16px 0 6px; padding-bottom: 3px; border-bottom: 2px solid #e2e8f0; color: #334155; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 3px 6px; text-align: left; font-size: 9px; }
        th { background: #f1f5f9; font-weight: bold; color: #475569; }
        tr:nth-child(even) { background: #f8fafc; }
        .bio-table { margin-bottom: 16px; }
        .bio-table td { border: none; padding: 2px 8px; }
        .bio-table .label { font-weight: bold; width: 160px; color: #475569; }
        .bio-table .sep { width: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .empty-note { color: #94a3b8; font-style: italic; font-size: 9px; margin-bottom: 12px; }
        .page-break { page-break-before: always; }
        .badge { padding: 1px 5px; font-size: 8px; border-radius: 3px; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-amber { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <h1>PROFIL PEGAWAI</h1>
    <p class="subtitle">Sistem Informasi Manajemen Pegawai</p>
    <p class="date">Dicetak: {{ date('d/m/Y H:i') }}</p>

    {{-- Biodata --}}
    <div class="section-title">Data Pribadi</div>
    <table class="bio-table">
        <tr>
            <td class="label">NIP</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->nip }}</td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="sep">:</td>
            <td>{{ implode(' ', array_filter([$pegawai->gelar_depan, $pegawai->nama_lengkap, $pegawai->gelar_belakang])) }}</td>
        </tr>
        <tr>
            <td class="label">Tempat, Tanggal Lahir</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->tempat_lahir }}, {{ $pegawai->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->jenisKelamin?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Agama</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->agama?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Pernikahan</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->statusPernikahan?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Golongan Darah</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->golonganDarah?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Telepon</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->no_telepon ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->email ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NPWP</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->npwp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Karpeg</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->no_karpeg ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Taspen</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->no_taspen ?? '-' }}</td>
        </tr>
    </table>

    {{-- Kepegawaian --}}
    <div class="section-title">Data Kepegawaian</div>
    <table class="bio-table">
        <tr>
            <td class="label">Tipe Pegawai</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->tipePegawai?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Kepegawaian</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->statusKepegawaian?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Unit Kerja</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->unitKerja?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Bagian</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->bagian?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">TMT CPNS</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->tmt_cpns?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">TMT PNS</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->tmt_pns?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Masa Kerja</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->masa_kerja }}</td>
        </tr>
        <tr>
            <td class="label">Pangkat Terakhir</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->pangkat_terakhir ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan Terakhir</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->jabatan_terakhir ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Gaji Pokok</td>
            <td class="sep">:</td>
            <td>Rp {{ number_format($pegawai->gaji_pokok ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td class="sep">:</td>
            <td>{{ $pegawai->is_active ? 'Aktif' : ($pegawai->tmt_pensiun ? 'Pensiun' : 'Tidak Aktif') }}</td>
        </tr>
    </table>

    {{-- Riwayat Pangkat --}}
    <div class="section-title">Riwayat Pangkat</div>
    @if($pegawai->riwayatPangkat->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Golongan / Pangkat</th>
                <th>Nomor SK</th>
                <th class="text-center">TMT Pangkat</th>
                <th class="text-center">Tanggal SK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->riwayatPangkat->sortByDesc('tmt_pangkat')->values() as $idx => $rp)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $rp->golongan?->label ?? '-' }}</td>
                <td>{{ $rp->nomor_sk ?? '-' }}</td>
                <td class="text-center">{{ $rp->tmt_pangkat?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-center">{{ $rp->tanggal_sk?->format('d/m/Y') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data riwayat pangkat.</p>
    @endif

    {{-- Riwayat Jabatan --}}
    <div class="section-title">Riwayat Jabatan</div>
    @if($pegawai->riwayatJabatan->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Jabatan</th>
                <th>Nomor SK</th>
                <th class="text-center">TMT Jabatan</th>
                <th class="text-center">Tanggal SK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->riwayatJabatan->sortByDesc('tmt_jabatan')->values() as $idx => $rj)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $rj->jabatan?->nama_jabatan ?? '-' }}</td>
                <td>{{ $rj->nomor_sk ?? '-' }}</td>
                <td class="text-center">{{ $rj->tmt_jabatan?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-center">{{ $rj->tanggal_sk?->format('d/m/Y') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data riwayat jabatan.</p>
    @endif

    <div class="page-break"></div>

    {{-- Riwayat KGB --}}
    <div class="section-title">Riwayat Kenaikan Gaji Berkala</div>
    @if($pegawai->riwayatKgb->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nomor SK</th>
                <th class="text-center">TMT KGB</th>
                <th class="text-right">Gaji Lama</th>
                <th class="text-right">Gaji Baru</th>
                <th class="text-center">MKG (Thn/Bln)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->riwayatKgb->sortByDesc('tmt_kgb')->values() as $idx => $rk)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $rk->nomor_sk ?? '-' }}</td>
                <td class="text-center">{{ $rk->tmt_kgb?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-right">{{ number_format($rk->gaji_lama ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($rk->gaji_baru ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ $rk->masa_kerja_golongan_tahun ?? 0 }} / {{ $rk->masa_kerja_golongan_bulan ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data riwayat KGB.</p>
    @endif

    {{-- Riwayat Pendidikan --}}
    <div class="section-title">Riwayat Pendidikan</div>
    @if($pegawai->riwayatPendidikan->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tingkat</th>
                <th>Institusi</th>
                <th>Jurusan</th>
                <th class="text-center">Tahun Lulus</th>
                <th>No. Ijazah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->riwayatPendidikan->sortByDesc('tahun_lulus')->values() as $idx => $rp)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $rp->tingkat_pendidikan ?? '-' }}</td>
                <td>{{ $rp->institusi ?? '-' }}</td>
                <td>{{ $rp->jurusan ?? '-' }}</td>
                <td class="text-center">{{ $rp->tahun_lulus ?? '-' }}</td>
                <td>{{ $rp->no_ijazah ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data riwayat pendidikan.</p>
    @endif

    {{-- Riwayat Latihan Jabatan --}}
    <div class="section-title">Riwayat Diklat / Latihan Jabatan</div>
    @if($pegawai->riwayatLatihanJabatan->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Latihan</th>
                <th class="text-center">Tahun</th>
                <th class="text-center">Jumlah Jam</th>
                <th>Penyelenggara</th>
                <th>No. Sertifikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->riwayatLatihanJabatan->sortByDesc('tahun_pelaksanaan')->values() as $idx => $rl)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $rl->nama_latihan ?? '-' }}</td>
                <td class="text-center">{{ $rl->tahun_pelaksanaan ?? '-' }}</td>
                <td class="text-center">{{ $rl->jumlah_jam ?? '-' }}</td>
                <td>{{ $rl->penyelenggara ?? '-' }}</td>
                <td>{{ $rl->no_sertifikat ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data diklat / latihan jabatan.</p>
    @endif

    {{-- Hukuman Disiplin --}}
    <div class="section-title">Riwayat Hukuman Disiplin</div>
    @if($pegawai->riwayatHukumanDisiplin->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tingkat</th>
                <th>Jenis Sanksi</th>
                <th>Nomor SK</th>
                <th class="text-center">TMT Hukuman</th>
                <th class="text-center">TMT Selesai</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->riwayatHukumanDisiplin->sortByDesc('tmt_hukuman')->values() as $idx => $rh)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $rh->tingkat_hukuman?->label() ?? '-' }}</td>
                <td>{{ $rh->jenis_sanksi?->label() ?? '-' }}</td>
                <td>{{ $rh->nomor_sk ?? '-' }}</td>
                <td class="text-center">{{ $rh->tmt_hukuman?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-center">{{ $rh->tmt_selesai_hukuman?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-center">
                    @if($rh->isAktif())
                        <span class="badge badge-red">Aktif</span>
                    @else
                        <span class="badge badge-green">Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data hukuman disiplin.</p>
    @endif

    {{-- Penilaian Kinerja --}}
    <div class="section-title">Penilaian Kinerja (SKP)</div>
    @if($pegawai->penilaianKinerja->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Tahun</th>
                <th class="text-center">Nilai SKP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->penilaianKinerja->sortByDesc('tahun')->values() as $idx => $pk)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center">{{ $pk->tahun ?? '-' }}</td>
                <td class="text-center">{{ $pk->nilai_skp ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data penilaian kinerja.</p>
    @endif

    {{-- Penghargaan --}}
    <div class="section-title">Riwayat Penghargaan</div>
    @if($pegawai->riwayatPenghargaan->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Penghargaan</th>
                <th class="text-center">Tahun</th>
                <th>Nomor SK</th>
                <th class="text-center">Tanggal SK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai->riwayatPenghargaan->sortByDesc('tahun')->values() as $idx => $rph)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $rph->nama_penghargaan ?? '-' }}</td>
                <td class="text-center">{{ $rph->tahun ?? '-' }}</td>
                <td>{{ $rph->nomor_sk ?? '-' }}</td>
                <td class="text-center">{{ $rph->tanggal_sk?->format('d/m/Y') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">Belum ada data penghargaan.</p>
    @endif
</body>
</html>

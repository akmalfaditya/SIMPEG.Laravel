<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Summary</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #1e293b; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 2px; }
        .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 16px; }
        .date { text-align: center; font-size: 9px; color: #94a3b8; margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: bold; margin: 18px 0 8px; padding-bottom: 4px; border-bottom: 2px solid #e2e8f0; color: #334155; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 4px 6px; text-align: left; }
        th { background: #f1f5f9; font-weight: bold; font-size: 9px; color: #475569; }
        td { font-size: 9px; }
        tr:nth-child(even) { background: #f8fafc; }
        .stats-grid { margin-bottom: 16px; }
        .stats-grid table { border: none; }
        .stats-grid td { border: 1px solid #e2e8f0; text-align: center; padding: 8px; }
        .stats-grid .label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stats-grid .value { font-size: 16px; font-weight: bold; }
        .text-blue { color: #2563eb; }
        .text-indigo { color: #4f46e5; }
        .text-pink { color: #db2777; }
        .text-cyan { color: #0891b2; }
        .text-teal { color: #0d9488; }
        .text-amber { color: #d97706; }
        .alert-hitam { background: #1e293b; color: white; padding: 2px 6px; font-size: 8px; }
        .alert-merah { background: #fef2f2; color: #dc2626; padding: 2px 6px; font-size: 8px; }
        .alert-kuning { background: #fefce8; color: #ca8a04; padding: 2px 6px; font-size: 8px; }
        .alert-hijau { background: #f0fdf4; color: #16a34a; padding: 2px 6px; font-size: 8px; }
        .eligible { color: #dc2626; font-weight: bold; }
        .upcoming { color: #d97706; }
        .filter-info { background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 10px; margin-bottom: 16px; font-size: 9px; color: #475569; }
        .page-break { page-break-before: always; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        tfoot td { font-weight: bold; background: #f1f5f9; }
    </style>
</head>
<body>
    <h1>LAPORAN DASHBOARD SIMPEG</h1>
    <p class="subtitle">Sistem Informasi Manajemen Pegawai</p>
    <p class="date">Dicetak: {{ date('d/m/Y H:i') }}</p>

    @if(!empty(array_filter($data['filters'])))
    <div class="filter-info">
        <strong>Filter Aktif:</strong>
        @if(!empty($data['filters']['unit_kerja'])) Unit Kerja: {{ $data['filters']['unit_kerja'] }} | @endif
        @if(!empty($data['filters']['golongan'])) Golongan: {{ $data['filters']['golongan'] }} | @endif
        @if(!empty($data['filters']['tmt_cpns_from'])) TMT CPNS Dari: {{ $data['filters']['tmt_cpns_from'] }} | @endif
        @if(!empty($data['filters']['tmt_cpns_to'])) TMT CPNS Sampai: {{ $data['filters']['tmt_cpns_to'] }} @endif
    </div>
    @endif

    {{-- Statistik Utama --}}
    <div class="section-title">Statistik Utama</div>
    <div class="stats-grid">
        <table>
            <tr>
                <td>
                    <div class="label">Total Pegawai</div>
                    <div class="value text-blue">{{ $data['total_pegawai'] }}</div>
                </td>
                <td>
                    <div class="label">Laki-Laki</div>
                    <div class="value text-indigo">{{ $data['stats']['total_laki'] }}</div>
                </td>
                <td>
                    <div class="label">Perempuan</div>
                    <div class="value text-pink">{{ $data['stats']['total_perempuan'] }}</div>
                </td>
                <td>
                    <div class="label">Rata-rata Usia</div>
                    <div class="value text-cyan">{{ $data['stats']['rata_rata_usia'] }}</div>
                </td>
                <td>
                    <div class="label">Rata-rata Masa Kerja</div>
                    <div class="value text-teal" style="font-size:12px">{{ $data['stats']['rata_rata_masa_kerja'] }}</div>
                </td>
                <td>
                    <div class="label">Total Alert</div>
                    <div class="value text-amber">{{ $data['kgb_alert_count'] + $data['pensiun_alert_count'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Distribusi Golongan --}}
    <div class="section-title">Distribusi Golongan</div>
    <table>
        <thead><tr><th>Golongan</th><th class="text-center">Jumlah</th><th class="text-center">Persentase</th></tr></thead>
        <tbody>
            @php $totalGol = array_sum($data['charts']['golongan']); @endphp
            @foreach($data['charts']['golongan'] as $label => $count)
            <tr>
                <td>{{ $label }}</td>
                <td class="text-center">{{ $count }}</td>
                <td class="text-center">{{ $totalGol > 0 ? round($count / $totalGol * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr><td>Total</td><td class="text-center">{{ $totalGol }}</td><td class="text-center">100%</td></tr></tfoot>
    </table>

    {{-- Distribusi Pendidikan --}}
    <div class="section-title">Distribusi Pendidikan Terakhir</div>
    <table>
        <thead><tr><th>Tingkat Pendidikan</th><th class="text-center">Jumlah</th><th class="text-center">Persentase</th></tr></thead>
        <tbody>
            @php $totalEdu = array_sum($data['advanced_charts']['pendidikan']); @endphp
            @foreach($data['advanced_charts']['pendidikan'] as $label => $count)
            <tr>
                <td>{{ $label }}</td>
                <td class="text-center">{{ $count }}</td>
                <td class="text-center">{{ $totalEdu > 0 ? round($count / $totalEdu * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr><td>Total</td><td class="text-center">{{ $totalEdu }}</td><td class="text-center">100%</td></tr></tfoot>
    </table>

    {{-- Distribusi Masa Kerja --}}
    <div class="section-title">Distribusi Masa Kerja</div>
    <table>
        <thead><tr><th>Rentang Masa Kerja</th><th class="text-center">Jumlah</th><th class="text-center">Persentase</th></tr></thead>
        <tbody>
            @php $totalMk = array_sum($data['advanced_charts']['masa_kerja']); @endphp
            @foreach($data['advanced_charts']['masa_kerja'] as $label => $count)
            <tr>
                <td>{{ $label }}</td>
                <td class="text-center">{{ $count }}</td>
                <td class="text-center">{{ $totalMk > 0 ? round($count / $totalMk * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr><td>Total</td><td class="text-center">{{ $totalMk }}</td><td class="text-center">100%</td></tr></tfoot>
    </table>

    <div class="page-break"></div>

    {{-- Rekapitulasi Per Unit Kerja --}}
    <div class="section-title">Rekapitulasi Per Unit Kerja</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Unit Kerja</th>
                <th class="text-center">Total</th>
                <th class="text-center">Laki-Laki</th>
                <th class="text-center">Perempuan</th>
                <th class="text-center">Rata-rata Usia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['summary_per_unit'] as $idx => $unit)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $unit['unit_kerja'] }}</td>
                <td class="text-center">{{ $unit['total'] }}</td>
                <td class="text-center">{{ $unit['laki'] }}</td>
                <td class="text-center">{{ $unit['perempuan'] }}</td>
                <td class="text-center">{{ $unit['rata_rata_usia'] }} thn</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td class="text-center">{{ collect($data['summary_per_unit'])->sum('total') }}</td>
                <td class="text-center">{{ collect($data['summary_per_unit'])->sum('laki') }}</td>
                <td class="text-center">{{ collect($data['summary_per_unit'])->sum('perempuan') }}</td>
                <td class="text-center">{{ $data['stats']['rata_rata_usia'] }} thn</td>
            </tr>
        </tfoot>
    </table>

    {{-- Proyeksi Pensiun --}}
    <div class="section-title">Proyeksi Pensiun 5 Tahun</div>
    <table>
        <thead><tr><th>Tahun</th><th class="text-center">Jumlah Pegawai Pensiun</th></tr></thead>
        <tbody>
            @foreach($data['advanced_charts']['pensiun_proyeksi'] as $tahun => $count)
            <tr>
                <td>{{ $tahun }}</td>
                <td class="text-center">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr><td>Total</td><td class="text-center">{{ array_sum($data['advanced_charts']['pensiun_proyeksi']) }}</td></tr></tfoot>
    </table>

    {{-- Tren KGB --}}
    <div class="section-title">Tren KGB 12 Bulan Ke Depan</div>
    <table>
        <thead><tr><th>Bulan</th><th class="text-center">Jumlah Jatuh Tempo KGB</th></tr></thead>
        <tbody>
            @foreach($data['advanced_charts']['kgb_tren'] as $bulan => $count)
            <tr>
                <td>{{ $bulan }}</td>
                <td class="text-center">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr><td>Total</td><td class="text-center">{{ array_sum($data['advanced_charts']['kgb_tren']) }}</td></tr></tfoot>
    </table>

    {{-- Top 10 KGB Alerts --}}
    @if(count($data['kgb_alerts']) > 0)
    <div class="section-title">Top 10 Alert KGB</div>
    <table>
        <thead><tr><th>NIP</th><th>Nama</th><th>Pangkat</th><th>Jatuh Tempo</th><th>Hari</th><th>Status</th></tr></thead>
        <tbody>
            @foreach($data['kgb_alerts'] as $a)
            <tr>
                <td>{{ $a['nip'] }}</td>
                <td>{{ $a['nama_lengkap'] }}</td>
                <td>{{ $a['pangkat_terakhir'] }}</td>
                <td>{{ $a['tanggal_jatuh_tempo']->format('d/m/Y') }}</td>
                <td class="text-center">{{ $a['hari_menuju_jatuh_tempo'] }}</td>
                <td class="{{ $a['is_eligible'] ? 'eligible' : 'upcoming' }}">{{ $a['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Top 10 Pensiun Alerts --}}
    @if(count($data['pensiun_alerts']) > 0)
    <div class="section-title">Top 10 Alert Pensiun</div>
    <table>
        <thead><tr><th>NIP</th><th>Nama</th><th>Jabatan</th><th>Tgl Pensiun</th><th>Bulan</th><th>Level</th></tr></thead>
        <tbody>
            @foreach($data['pensiun_alerts'] as $a)
            @php $alertClass = match($a['alert_level']) { 'Hitam' => 'alert-hitam', 'Merah' => 'alert-merah', 'Kuning' => 'alert-kuning', 'Hijau' => 'alert-hijau', default => '' }; @endphp
            <tr>
                <td>{{ $a['nip'] }}</td>
                <td>{{ $a['nama_lengkap'] }}</td>
                <td>{{ $a['jabatan_terakhir'] }}</td>
                <td>{{ $a['tanggal_pensiun']->format('d/m/Y') }}</td>
                <td class="text-center">{{ $a['bulan_menuju_pensiun'] }}</td>
                <td><span class="{{ $alertClass }}">{{ $a['alert_level'] }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>

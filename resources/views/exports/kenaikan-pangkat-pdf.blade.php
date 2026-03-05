<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 4px; }
        .date { text-align: center; font-size: 9px; color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background: #e2e8f0; font-weight: bold; font-size: 9px; }
        tr:nth-child(even) { background: #f8fafc; }
        .eligible { color: #16a34a; font-weight: bold; }
        .not-eligible { color: #dc2626; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="date">Dicetak: {{ date('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>NIP</th><th>Nama</th><th>Gol. Saat Ini</th><th>Gol. Berikutnya</th><th>MK Gol.</th><th>MK</th><th>SKP</th><th>Latihan</th><th>Disiplin</th><th>Status</th><th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $c)
            <tr>
                <td>{{ $c['nip'] }}</td>
                <td>{{ $c['nama_lengkap'] }}</td>
                <td>{{ $c['golongan_saat_ini'] }}</td>
                <td>{{ $c['golongan_berikutnya'] }}</td>
                <td>{{ $c['masa_kerja_golongan'] }}</td>
                <td>{{ $c['syarat_masa_kerja'] ? '✓' : '✗' }}</td>
                <td>{{ $c['syarat_skp'] ? '✓' : '✗' }}</td>
                <td>{{ $c['syarat_latihan'] ? '✓' : '✗' }}</td>
                <td>{{ $c['syarat_hukuman'] ? '✓' : '✗' }}</td>
                <td class="{{ $c['is_eligible'] ? 'eligible' : 'not-eligible' }}">{{ $c['is_eligible'] ? 'Eligible' : 'Belum' }}</td>
                <td style="font-size:8px">{{ $c['alasan_tidak_eligible'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

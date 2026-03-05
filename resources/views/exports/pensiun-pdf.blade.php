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
        .hitam { background: #1e293b; color: #fff; }
        .merah { color: #dc2626; font-weight: bold; }
        .kuning { color: #d97706; }
        .hijau { color: #16a34a; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="date">Dicetak: {{ date('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>NIP</th><th>Nama</th><th>Jabatan</th><th>BUP</th><th>Tgl Pensiun</th><th>Sisa (Bulan)</th><th>Level</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $a)
            <tr>
                <td>{{ $a['nip'] }}</td>
                <td>{{ $a['nama_lengkap'] }}</td>
                <td>{{ $a['jabatan_terakhir'] }}</td>
                <td>{{ $a['bup'] }}</td>
                <td>{{ $a['tanggal_pensiun']->format('d/m/Y') }}</td>
                <td>{{ $a['bulan_menuju_pensiun'] }}</td>
                <td class="{{ strtolower($a['alert_level']) }}">{{ $a['alert_level'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

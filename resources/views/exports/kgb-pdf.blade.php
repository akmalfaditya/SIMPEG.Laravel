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
        .eligible { color: #dc2626; font-weight: bold; }
        .upcoming { color: #d97706; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="date">Dicetak: {{ date('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>NIP</th><th>Nama</th><th>Pangkat</th><th>TMT KGB Terakhir</th><th>Jatuh Tempo</th><th>Hari Menuju</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $a)
            <tr>
                <td>{{ $a['nip'] }}</td>
                <td>{{ $a['nama_lengkap'] }}</td>
                <td>{{ $a['pangkat_terakhir'] }}</td>
                <td>{{ $a['tmt_kgb_terakhir']->format('d/m/Y') }}</td>
                <td>{{ $a['tanggal_jatuh_tempo']->format('d/m/Y') }}</td>
                <td>{{ $a['hari_menuju_jatuh_tempo'] }}</td>
                <td class="{{ $a['is_eligible'] ? 'eligible' : 'upcoming' }}">{{ $a['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

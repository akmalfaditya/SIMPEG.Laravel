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
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="date">Dicetak: {{ date('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>NIP</th><th>Nama</th><th>Pangkat</th><th>Jabatan</th><th>Masa Kerja</th><th>Milestone</th><th>Penghargaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $c)
            <tr>
                <td>{{ $c['nip'] }}</td>
                <td>{{ $c['nama_lengkap'] }}</td>
                <td>{{ $c['pangkat_terakhir'] }}</td>
                <td>{{ $c['jabatan_terakhir'] }}</td>
                <td>{{ $c['masa_kerja_tahun'] }} tahun</td>
                <td>{{ $c['milestone'] }} Tahun</td>
                <td>{{ $c['nama_penghargaan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

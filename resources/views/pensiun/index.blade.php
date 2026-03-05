@extends('layouts.app')
@section('title', 'Pensiun')
@section('header', 'Alert Pensiun')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jabatan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">BUP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Tgl Pensiun</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Sisa (Bulan)</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Level</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @php $colors = ['Hitam' => 'bg-slate-800 text-white', 'Merah' => 'bg-red-100 text-red-700', 'Kuning' => 'bg-yellow-100 text-yellow-700', 'Hijau' => 'bg-green-100 text-green-700']; @endphp
                @forelse($alerts as $a)
                <tr class="hover:bg-slate-50"><td class="px-4 py-2.5 font-mono text-xs">{{ $a['nip'] }}</td><td class="px-4 py-2.5 font-medium text-slate-700"><a href="{{ route('pegawai.show', $a['pegawai_id']) }}" class="hover:text-blue-600">{{ $a['nama_lengkap'] }}</a></td><td class="px-4 py-2.5">{{ $a['jabatan_terakhir'] }}</td><td class="px-4 py-2.5">{{ $a['bup'] }}</td><td class="px-4 py-2.5">{{ $a['tanggal_pensiun']->format('d/m/Y') }}</td><td class="px-4 py-2.5">{{ $a['bulan_menuju_pensiun'] }}</td>
                <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $colors[$a['alert_level']] ?? '' }}">{{ $a['alert_level'] }}</span></td></tr>
                @empty <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'KGB')
@section('header', $filterTitle ?? 'Monitoring KGB')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex items-center gap-3 flex-wrap">
        <a href="{{ route('kgb.index') }}" class="px-3 py-1.5 text-xs rounded-lg {{ !$filterTitle ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
        <a href="{{ route('kgb.upcoming') }}" class="px-3 py-1.5 text-xs rounded-lg {{ $filterTitle && str_contains($filterTitle, 'H-60') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">H-60 Hari</a>
        <a href="{{ route('kgb.eligible') }}" class="px-3 py-1.5 text-xs rounded-lg {{ $filterTitle && str_contains($filterTitle, 'Eligible') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Eligible</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Pangkat</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">TMT KGB Terakhir</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jatuh Tempo</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Status</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($alerts as $a)
                <tr class="hover:bg-slate-50"><td class="px-4 py-2.5 font-mono text-xs">{{ $a['nip'] }}</td><td class="px-4 py-2.5 font-medium text-slate-700"><a href="{{ route('pegawai.show', $a['pegawai_id']) }}" class="hover:text-blue-600">{{ $a['nama_lengkap'] }}</a></td><td class="px-4 py-2.5">{{ $a['pangkat_terakhir'] }}</td><td class="px-4 py-2.5">{{ $a['tmt_kgb_terakhir']->format('d/m/Y') }}</td><td class="px-4 py-2.5">{{ $a['tanggal_jatuh_tempo']->format('d/m/Y') }}</td>
                <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $a['is_eligible'] ? 'bg-red-100 text-red-700' : ($a['hari_menuju_jatuh_tempo'] <= 60 ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">{{ $a['status'] }}</span></td></tr>
                @empty <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

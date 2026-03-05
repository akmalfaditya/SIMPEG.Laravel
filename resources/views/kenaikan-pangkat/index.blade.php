@extends('layouts.app')
@section('title', 'Kenaikan Pangkat')
@section('header', $filterTitle ?? 'Kenaikan Pangkat')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex items-center gap-3">
        <a href="{{ route('kenaikan-pangkat.index') }}" class="px-3 py-1.5 text-xs rounded-lg {{ !$filterTitle ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
        <a href="{{ route('kenaikan-pangkat.eligible') }}" class="px-3 py-1.5 text-xs rounded-lg {{ $filterTitle ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Eligible</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Golongan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Berikutnya</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Masa Kerja</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">MK</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">SKP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Latihan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Disiplin</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Status</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($candidates as $c)
                <tr class="hover:bg-slate-50"><td class="px-4 py-2.5 font-medium text-slate-700"><a href="{{ route('pegawai.show', $c['pegawai_id']) }}" class="hover:text-blue-600">{{ $c['nama_lengkap'] }}</a></td><td class="px-4 py-2.5">{{ $c['golongan_saat_ini'] }}</td><td class="px-4 py-2.5 font-medium text-blue-600">{{ $c['golongan_berikutnya'] }}</td><td class="px-4 py-2.5 text-xs">{{ $c['masa_kerja_golongan'] }}</td>
                <td class="px-4 py-2.5">{!! $c['syarat_masa_kerja'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                <td class="px-4 py-2.5">{!! $c['syarat_skp'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                <td class="px-4 py-2.5">{!! $c['syarat_latihan'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                <td class="px-4 py-2.5">{!! $c['syarat_hukuman'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $c['is_eligible'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $c['is_eligible'] ? 'Eligible' : 'Belum' }}</span></td></tr>
                @empty <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

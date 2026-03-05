@extends('layouts.app')
@section('title', 'Satyalencana')
@section('header', 'Kandidat Satyalencana')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex items-center gap-3 flex-wrap">
        <a href="{{ route('satyalencana.index') }}" class="px-3 py-1.5 text-xs rounded-lg {{ !$selectedMilestone ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
        <a href="{{ route('satyalencana.index', ['milestone' => 10]) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $selectedMilestone == 10 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">10 Tahun</a>
        <a href="{{ route('satyalencana.index', ['milestone' => 20]) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $selectedMilestone == 20 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">20 Tahun</a>
        <a href="{{ route('satyalencana.index', ['milestone' => 30]) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $selectedMilestone == 30 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">30 Tahun</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Pangkat</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jabatan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Masa Kerja</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Milestone</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Penghargaan</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($candidates as $c)
                <tr class="hover:bg-slate-50"><td class="px-4 py-2.5 font-mono text-xs">{{ $c['nip'] }}</td><td class="px-4 py-2.5 font-medium text-slate-700"><a href="{{ route('pegawai.show', $c['pegawai_id']) }}" class="hover:text-blue-600">{{ $c['nama_lengkap'] }}</a></td><td class="px-4 py-2.5">{{ $c['pangkat_terakhir'] }}</td><td class="px-4 py-2.5">{{ $c['jabatan_terakhir'] }}</td><td class="px-4 py-2.5">{{ $c['masa_kerja_tahun'] }} tahun</td>
                <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $c['milestone'] == 30 ? 'bg-amber-100 text-amber-700' : ($c['milestone'] == 20 ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">{{ $c['milestone'] }} Tahun</span></td>
                <td class="px-4 py-2.5 text-xs">{{ $c['nama_penghargaan'] }}</td></tr>
                @empty <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Tidak ada kandidat.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

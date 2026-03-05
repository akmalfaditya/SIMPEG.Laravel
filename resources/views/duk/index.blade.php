@extends('layouts.app')
@section('title', 'DUK')
@section('header', 'Daftar Urut Kepangkatan (DUK)')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">No</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Golongan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jabatan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Masa Kerja</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Pendidikan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Usia</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($entries as $e)
                <tr class="hover:bg-slate-50"><td class="px-4 py-2.5 font-bold text-blue-600">{{ $e['ranking'] }}</td><td class="px-4 py-2.5 font-mono text-xs">{{ $e['nip'] }}</td><td class="px-4 py-2.5 font-medium text-slate-700"><a href="{{ route('pegawai.show', $e['pegawai_id']) }}" class="hover:text-blue-600">{{ $e['nama_lengkap'] }}</a></td><td class="px-4 py-2.5">{{ $e['golongan_ruang'] }}</td><td class="px-4 py-2.5">{{ $e['jabatan_terakhir'] }}</td><td class="px-4 py-2.5">{{ $e['masa_kerja'] }}</td><td class="px-4 py-2.5">{{ $e['pendidikan_terakhir'] }}</td><td class="px-4 py-2.5">{{ $e['usia'] }}</td></tr>
                @empty <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

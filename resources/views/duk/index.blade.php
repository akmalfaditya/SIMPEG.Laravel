@extends('layouts.app')
@section('title', 'DUK')
@section('header', 'Daftar Urut Kepangkatan (DUK)')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">DUK</span>
@endsection
@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="text-sm font-medium text-slate-700">Total: {{ $entries->total() }} pegawai</div>
            <div class="sm:ml-auto flex items-center gap-2">
                <form method="GET" class="inline-flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP/Nama..."
                        class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
                </form>
                <a href="{{ route('export', ['type' => 'duk', 'format' => 'pdf']) }}"
                    class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all">PDF</a>
                <a href="{{ route('export', ['type' => 'duk', 'format' => 'excel']) }}"
                    class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all">Excel</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="data-table">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Golongan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Masa Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Pendidikan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Usia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="table-body">
                    @forelse($entries as $e)
                        <tr class="hover:bg-slate-50 data-row">
                            <td class="px-4 py-2.5 font-bold text-blue-600">{{ $e['ranking'] }}</td>
                            <td class="px-4 py-2.5 font-mono text-xs">{{ $e['nip'] }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-700"><a
                                    href="{{ route('pegawai.show', $e['pegawai_id']) }}"
                                    class="hover:text-blue-600">{{ $e['nama_lengkap'] }}</a></td>
                            <td class="px-4 py-2.5">{{ $e['golongan_ruang'] }}</td>
                            <td class="px-4 py-2.5">{{ $e['jabatan_terakhir'] }}</td>
                            <td class="px-4 py-2.5">{{ $e['masa_kerja'] }}</td>
                            <td class="px-4 py-2.5">{{ $e['pendidikan_terakhir'] }}</td>
                            <td class="px-4 py-2.5">{{ $e['usia'] }}</td>
                        </tr>
                    @empty <tr class="empty-row">
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
            <span class="text-xs text-slate-500">Menampilkan
                {{ $entries->firstItem() ?? 0 }}–{{ $entries->lastItem() ?? 0 }} dari {{ $entries->total() }}</span>
            {{ $entries->links() }}
        </div>
    </div>
@endsection

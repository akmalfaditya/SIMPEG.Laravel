@extends('layouts.app')
@section('title', 'Kenaikan Pangkat')
@section('header', $filterTitle ?? 'Kenaikan Pangkat')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">
        Dashboard</a> / <span class="text-slate-700">Kenaikan Pangkat</span>
@endsection
@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('kenaikan-pangkat.index', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ $activeFilter === 'semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                <a href="{{ route('kenaikan-pangkat.eligible', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ $activeFilter === 'eligible' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Eligible</a>
                <a href="{{ route('kenaikan-pangkat.ditunda', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ $activeFilter === 'ditunda' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-600' }}">Ditunda
                    (Hukdis)</a>
            </div>
            <div class="sm:ml-auto flex items-center gap-2">
                <form method="GET" class="inline-flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama..."
                        class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
                </form>
                <a href="{{ route('export', ['type' => 'kenaikan-pangkat', 'format' => 'pdf']) }}"
                    class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all">PDF</a>
                <a href="{{ route('export', ['type' => 'kenaikan-pangkat', 'format' => 'excel']) }}"
                    class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all">Excel</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="data-table">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Golongan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Berikutnya</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Masa Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">MK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">SKP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Latihan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Disiplin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Proyeksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="table-body">
                    @forelse($candidates as $c)
                        <tr class="hover:bg-slate-50 data-row">
                            <td class="px-4 py-2.5 font-medium text-slate-700"><a
                                    href="{{ route('pegawai.show', $c['pegawai_id']) }}"
                                    class="hover:text-blue-600">{{ $c['nama_lengkap'] }}</a></td>
                            <td class="px-4 py-2.5">{{ $c['golongan_saat_ini'] }}</td>
                            <td class="px-4 py-2.5 font-medium text-blue-600">{{ $c['golongan_berikutnya'] }}</td>
                            <td class="px-4 py-2.5 text-xs">{{ $c['masa_kerja_golongan'] }}</td>
                            <td class="px-4 py-2.5">{!! $c['syarat_masa_kerja'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                            <td class="px-4 py-2.5">{!! $c['syarat_skp'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                            <td class="px-4 py-2.5">{!! $c['syarat_latihan'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                            <td class="px-4 py-2.5">{!! $c['syarat_hukuman'] ? '<span class="text-emerald-500">✓</span>' : '<span class="text-red-500">✗</span>' !!}</td>
                            <td class="px-4 py-2.5 text-xs">{{ $c['proyeksi_periode'] }}</td>
                            <td class="px-4 py-2.5"><span
                                    class="px-2 py-1 text-xs rounded-full font-medium {{ $c['is_eligible'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $c['is_eligible'] ? 'Eligible' : 'Belum' }}</span>
                                @if ($c['hukdis_pangkat_flag'])
                                    <div class="mt-1"><span
                                            class="px-2 py-0.5 text-[10px] rounded-full font-bold bg-red-600 text-white">{{ $c['hukdis_pangkat_note'] }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if ($c['is_eligible'] && !$c['hukdis_pangkat_flag'])
                                    <a href="{{ route('kenaikan-pangkat.process.form', $c['pegawai_id']) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Proses
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty <tr class="empty-row">
                            <td colspan="11" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
            <span class="text-xs text-slate-500">Menampilkan
                {{ $candidates->firstItem() ?? 0 }}–{{ $candidates->lastItem() ?? 0 }} dari
                {{ $candidates->total() }}</span>
            {{ $candidates->links() }}
        </div>
    </div>
@endsection

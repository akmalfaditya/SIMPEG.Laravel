@extends('layouts.app')
@section('title', 'Pensiun')
@section('header', 'Alert Pensiun')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Pensiun</span>
@endsection
@section('content')
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-lg text-sm text-rose-700">{{ session('error') }}</div>
    @endif
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('pensiun.index', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ !($filterLevel ?? null) ? 'bg-blue-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                <a href="{{ route('pensiun.index', array_merge(['level' => 'Hitam'], request()->only('search'))) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ ($filterLevel ?? null) === 'Hitam' ? 'bg-blue-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Hitam
                    (Lewat)</a>
                <a href="{{ route('pensiun.index', array_merge(['level' => 'Merah'], request()->only('search'))) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ ($filterLevel ?? null) === 'Merah' ? 'bg-blue-800 text-white' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">Merah
                    (≤6 bln)</a>
                <a href="{{ route('pensiun.index', array_merge(['level' => 'Kuning'], request()->only('search'))) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ ($filterLevel ?? null) === 'Kuning' ? 'bg-blue-800 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }}">Kuning
                    (≤12 bln)</a>
                <a href="{{ route('pensiun.index', array_merge(['level' => 'Hijau'], request()->only('search'))) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ ($filterLevel ?? null) === 'Hijau' ? 'bg-blue-800 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">Hijau
                    (≤24 bln)</a>
            </div>
            <div class="sm:ml-auto flex items-center gap-2">
                <form method="GET" class="inline-flex">
                    @if ($filterLevel)
                        <input type="hidden" name="level" value="{{ $filterLevel }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP/Nama..."
                        class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-blue-800 w-48">
                </form>
                <a href="{{ route('export', ['type' => 'pensiun', 'format' => 'pdf']) }}"
                    class="px-3 py-1.5 text-xs bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-all">PDF</a>
                <a href="{{ route('export', ['type' => 'pensiun', 'format' => 'excel']) }}"
                    class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all">Excel</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="data-table">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">BUP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Tgl Pensiun</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Sisa (Bulan)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Level</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="table-body">
                    @php $colors = ['Hitam' => 'bg-slate-800 text-white', 'Merah' => 'bg-rose-100 text-rose-700', 'Kuning' => 'bg-yellow-100 text-yellow-700', 'Hijau' => 'bg-green-100 text-green-700']; @endphp
                    @forelse($alerts as $a)
                        <tr class="hover:bg-slate-50 data-row">
                            <td class="px-4 py-2.5 font-mono text-xs">{{ $a['nip'] }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-700"><a
                                    href="{{ route('pegawai.show', $a['pegawai_id']) }}"
                                    class="hover:text-blue-600">{{ $a['nama_lengkap'] }}</a></td>
                            <td class="px-4 py-2.5">{{ $a['jabatan_terakhir'] }}</td>
                            <td class="px-4 py-2.5">{{ $a['bup'] }}</td>
                            <td class="px-4 py-2.5">{{ $a['tanggal_pensiun']->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5">{{ $a['bulan_menuju_pensiun'] }}</td>
                            <td class="px-4 py-2.5"><span
                                    class="px-2 py-1 text-xs rounded-full font-medium {{ $colors[$a['alert_level']] ?? '' }}">{{ $a['alert_level'] }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if (in_array($a['alert_level'], ['Hitam', 'Merah']))
                                    <a href="{{ route('pensiun.process.form', $a['pegawai_id']) }}"
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
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
            <span class="text-xs text-slate-500">Menampilkan
                {{ $alerts->firstItem() ?? 0 }}–{{ $alerts->lastItem() ?? 0 }} dari {{ $alerts->total() }}</span>
            {{ $alerts->links() }}
        </div>
    </div>
@endsection

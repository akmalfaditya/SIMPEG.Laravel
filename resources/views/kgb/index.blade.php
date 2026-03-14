@extends('layouts.app')
@section('title', 'KGB')
@section('header', $filterTitle ?? 'Monitoring KGB')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">KGB</span>
@endsection
@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('kgb.index', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ !$filterTitle ? 'bg-blue-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                <a href="{{ route('kgb.upcoming', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ $filterTitle && str_contains($filterTitle, 'H-60') ? 'bg-blue-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">H-60
                    Hari</a>
                <a href="{{ route('kgb.eligible', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ $filterTitle && str_contains($filterTitle, 'Eligible') ? 'bg-blue-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Eligible</a>
                <a href="{{ route('kgb.ditunda', request()->only('search')) }}"
                    class="px-3 py-1.5 text-xs rounded-lg {{ $filterTitle && str_contains($filterTitle, 'Ditunda') ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">Ditunda
                    (Hukdis)</a>
            </div>
            <div class="sm:ml-auto flex items-center gap-2">
                <form method="GET" class="inline-flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP/Nama..."
                        class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-blue-800 w-48">
                </form>
                <a href="{{ route('export', ['type' => 'kgb', 'format' => 'pdf']) }}"
                    class="px-3 py-1.5 text-xs bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-all"
                    title="Export PDF">PDF</a>
                <a href="{{ route('export', ['type' => 'kgb', 'format' => 'excel']) }}"
                    class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all"
                    title="Export Excel">Excel</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="data-table">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Pangkat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">TMT KGB Terakhir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">Est. Gaji Baru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="table-body">
                    @forelse($alerts as $a)
                        <tr class="hover:bg-slate-50 data-row">
                            <td class="px-4 py-2.5 font-mono text-xs">{{ $a['nip'] }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-700"><a
                                    href="{{ route('pegawai.show', $a['pegawai_id']) }}"
                                    class="hover:text-blue-600">{{ $a['nama_lengkap'] }}</a></td>
                            <td class="px-4 py-2.5">{{ $a['pangkat_terakhir'] }}</td>
                            <td class="px-4 py-2.5">{{ $a['tmt_kgb_terakhir']->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5">{{ $a['tanggal_jatuh_tempo']->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono text-xs">Rp
                                {{ number_format($a['gaji_pokok'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono text-xs">
                                {{ $a['est_gaji_baru'] ? 'Rp ' . number_format($a['est_gaji_baru'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-2.5">
                                <span
                                    class="px-2 py-1 text-xs rounded-full font-medium {{ $a['status'] === 'Ditunda' ? 'bg-rose-100 text-rose-700' : ($a['is_eligible'] ? 'bg-rose-100 text-rose-700' : ($a['hari_menuju_jatuh_tempo'] <= 60 ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700')) }}">{{ $a['status'] }}</span>
                                @if ($a['hukdis_flag'])
                                    <div class="mt-1"><span
                                            class="px-2 py-0.5 text-[10px] rounded-full font-bold bg-rose-600 text-white">{{ $a['hukdis_note'] }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if ($a['is_eligible'] && !$a['hukdis_flag'])
                                    <a href="{{ route('kgb.process.form', $a['pegawai_id']) }}"
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
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td>
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

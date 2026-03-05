@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Pegawai</p>
                <p class="text-3xl font-bold text-slate-800 mt-1">{{ $data['total_pegawai'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">KGB Alert</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">{{ $data['kgb_alert_count'] }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v1"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Pensiun Alert</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $data['pensiun_alert_count'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Satyalencana</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $data['satyalencana_eligible_count'] }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Golongan</h3>
        <canvas id="chartGolongan" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Gender</h3>
        <canvas id="chartGender" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Usia</h3>
        <canvas id="chartUsia" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Unit Kerja</h3>
        <canvas id="chartUnitKerja" height="200"></canvas>
    </div>
</div>

{{-- Alert Tables --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- KGB Alerts --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">KGB Akan Datang</h3>
            <a href="{{ route('kgb.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr><th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Pegawai</th><th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Jatuh Tempo</th><th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Status</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse(array_slice($data['kgb_alerts'], 0, 5) as $alert)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5 font-medium text-slate-700">{{ $alert['nama_lengkap'] }}</td>
                        <td class="px-4 py-2.5 text-slate-600">{{ $alert['tanggal_jatuh_tempo']->format('d/m/Y') }}</td>
                        <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $alert['is_eligible'] ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">{{ $alert['status'] }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pensiun Alerts --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Alert Pensiun</h3>
            <a href="{{ route('pensiun.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr><th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Pegawai</th><th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Tgl Pensiun</th><th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Level</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse(array_slice($data['pensiun_alerts'], 0, 5) as $alert)
                    @php $colors = ['Hitam' => 'bg-slate-800 text-white', 'Merah' => 'bg-red-100 text-red-700', 'Kuning' => 'bg-yellow-100 text-yellow-700', 'Hijau' => 'bg-green-100 text-green-700']; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5 font-medium text-slate-700">{{ $alert['nama_lengkap'] }}</td>
                        <td class="px-4 py-2.5 text-slate-600">{{ $alert['tanggal_pensiun']->format('d/m/Y') }}</td>
                        <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $colors[$alert['alert_level']] ?? '' }}">{{ $alert['alert_level'] }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const colors = ['#3b82f6','#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#64748b','#f97316','#14b8a6','#6366f1','#d946ef','#84cc16','#0ea5e9','#e11d48','#a855f7','#22d3ee'];
function makeChart(id, type, labels, data, label) {
    new Chart(document.getElementById(id), {
        type: type,
        data: { labels: labels, datasets: [{ label: label, data: data, backgroundColor: colors.slice(0, data.length), borderWidth: 0, borderRadius: type === 'bar' ? 6 : 0 }] },
        options: { responsive: true, plugins: { legend: { display: type !== 'bar', position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } } }, scales: type === 'bar' ? { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { ticks: { font: { size: 10 } } } } : {} }
    });
}

const charts = @json($data['charts']);
makeChart('chartGolongan', 'bar', Object.keys(charts.golongan), Object.values(charts.golongan), 'Jumlah');
makeChart('chartGender', 'doughnut', Object.keys(charts.gender), Object.values(charts.gender), 'Jumlah');
makeChart('chartUsia', 'bar', Object.keys(charts.usia), Object.values(charts.usia), 'Jumlah');
makeChart('chartUnitKerja', 'doughnut', Object.keys(charts.unit_kerja), Object.values(charts.unit_kerja), 'Jumlah');
</script>
@endpush

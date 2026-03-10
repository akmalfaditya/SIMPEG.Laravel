@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
{{-- Filter Panel --}}
<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm mb-6">
    <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter Dashboard
            </h3>
            <div class="flex items-center gap-2">
                @if($hasFilters)
                <a href="{{ route('dashboard') }}" class="text-xs text-red-600 hover:text-red-700 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset Filter
                </a>
                @endif
                <a href="{{ route('dashboard.export-pdf', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Unit Kerja</label>
                <select name="unit_kerja" class="w-full rounded-lg border border-slate-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Unit</option>
                    @foreach($filterOptions['unit_kerja_list'] as $id => $nama)
                    <option value="{{ $id }}" {{ request('unit_kerja') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Golongan</label>
                <select name="golongan" class="w-full rounded-lg border border-slate-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Golongan</option>
                    @foreach($filterOptions['golongan_list'] as $val => $label)
                    <option value="{{ $val }}" {{ request('golongan') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">TMT CPNS Dari</label>
                <input type="date" name="tmt_cpns_from" value="{{ request('tmt_cpns_from') }}" class="w-full rounded-lg border border-slate-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="document.getElementById('filterForm').submit()">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">TMT CPNS Sampai</label>
                <input type="date" name="tmt_cpns_to" value="{{ request('tmt_cpns_to') }}" class="w-full rounded-lg border border-slate-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="document.getElementById('filterForm').submit()">
            </div>
        </div>
    </form>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Pegawai</p>
                <p class="text-3xl font-bold text-slate-800 mt-1">{{ $data['total_pegawai'] }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Laki-Laki</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $data['stats']['total_laki'] }}</p>
            </div>
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Perempuan</p>
                <p class="text-3xl font-bold text-pink-600 mt-1">{{ $data['stats']['total_perempuan'] }}</p>
            </div>
            <div class="w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Rata-rata Usia</p>
                <p class="text-3xl font-bold text-cyan-600 mt-1">{{ $data['stats']['rata_rata_usia'] }}</p>
            </div>
            <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Rata-rata MK</p>
                <p class="text-lg font-bold text-teal-600 mt-1">{{ $data['stats']['rata_rata_masa_kerja'] }}</p>
            </div>
            <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Alerts</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">{{ $data['kgb_alert_count'] + $data['pensiun_alert_count'] }}</p>
            </div>
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Original 4 Charts --}}
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

{{-- Advanced Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-1">Tren KGB 12 Bulan Ke Depan</h3>
        <p class="text-xs text-slate-400 mb-4">Jumlah pegawai yang jatuh tempo KGB per bulan</p>
        <canvas id="chartKgbTren" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-1">Proyeksi Pensiun 5 Tahun</h3>
        <p class="text-xs text-slate-400 mb-4">Jumlah pegawai yang akan pensiun per tahun</p>
        <canvas id="chartPensiunProyeksi" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Pendidikan Terakhir</h3>
        <canvas id="chartPendidikan" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Jenis Jabatan</h3>
        <canvas id="chartJenisJabatan" height="200"></canvas>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm lg:col-span-2">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Masa Kerja</h3>
        <canvas id="chartMasaKerja" height="120"></canvas>
    </div>
</div>

{{-- Summary Per Unit Kerja --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-8">
    <div class="p-5 border-b border-slate-100">
        <h3 class="text-sm font-semibold text-slate-700">Rekapitulasi Per Unit Kerja</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">No</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">Unit Kerja</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-500">Total</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-500">Laki-Laki</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-500">Perempuan</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-500">Rata-rata Usia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($data['summary_per_unit'] as $idx => $unit)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5 text-slate-500">{{ $idx + 1 }}</td>
                    <td class="px-4 py-2.5 font-medium text-slate-700">{{ $unit['unit_kerja'] }}</td>
                    <td class="px-4 py-2.5 text-center font-semibold text-slate-700">{{ $unit['total'] }}</td>
                    <td class="px-4 py-2.5 text-center text-indigo-600">{{ $unit['laki'] }}</td>
                    <td class="px-4 py-2.5 text-center text-pink-600">{{ $unit['perempuan'] }}</td>
                    <td class="px-4 py-2.5 text-center text-slate-600">{{ $unit['rata_rata_usia'] }} thn</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
            @if(count($data['summary_per_unit']) > 0)
            <tfoot class="bg-slate-50 font-semibold">
                <tr>
                    <td class="px-4 py-2.5" colspan="2">Total</td>
                    <td class="px-4 py-2.5 text-center">{{ collect($data['summary_per_unit'])->sum('total') }}</td>
                    <td class="px-4 py-2.5 text-center text-indigo-600">{{ collect($data['summary_per_unit'])->sum('laki') }}</td>
                    <td class="px-4 py-2.5 text-center text-pink-600">{{ collect($data['summary_per_unit'])->sum('perempuan') }}</td>
                    <td class="px-4 py-2.5 text-center">{{ $data['stats']['rata_rata_usia'] }} thn</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Alert Tables --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- KGB Alerts --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">KGB Akan Datang <span class="text-xs font-normal text-slate-400">({{ $data['kgb_alert_count'] }} total)</span></h3>
            <a href="{{ route('kgb.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua &rarr;</a>
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
            <h3 class="text-sm font-semibold text-slate-700">Alert Pensiun <span class="text-xs font-normal text-slate-400">({{ $data['pensiun_alert_count'] }} total)</span></h3>
            <a href="{{ route('pensiun.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua &rarr;</a>
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
const blueGradient = ['#3b82f6','#2563eb','#1d4ed8','#1e40af','#1e3a8a','#60a5fa','#93c5fd','#bfdbfe','#dbeafe','#eff6ff','#1d4ed8','#3b82f6'];

function makeChart(id, type, labels, data, label, customColors) {
    const bgColors = customColors || colors.slice(0, data.length);
    new Chart(document.getElementById(id), {
        type: type,
        data: { labels: labels, datasets: [{ label: label, data: data, backgroundColor: bgColors, borderWidth: 0, borderRadius: type === 'bar' ? 6 : 0 }] },
        options: {
            responsive: true,
            plugins: { legend: { display: type !== 'bar', position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } } },
            scales: type === 'bar' ? { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } } : {}
        }
    });
}

function makeLineChart(id, labels, data, label, color) {
    new Chart(document.getElementById(id), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                borderColor: color || '#3b82f6',
                backgroundColor: (color || '#3b82f6') + '20',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: color || '#3b82f6',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 9 }, maxRotation: 45 } } }
        }
    });
}

const charts = @json($data['charts']);
const adv = @json($data['advanced_charts']);

makeChart('chartGolongan', 'bar', Object.keys(charts.golongan), Object.values(charts.golongan), 'Jumlah');
makeChart('chartGender', 'doughnut', Object.keys(charts.gender), Object.values(charts.gender), 'Jumlah');
makeChart('chartUsia', 'bar', Object.keys(charts.usia), Object.values(charts.usia), 'Jumlah');
makeChart('chartUnitKerja', 'doughnut', Object.keys(charts.unit_kerja), Object.values(charts.unit_kerja), 'Jumlah');

makeLineChart('chartKgbTren', Object.keys(adv.kgb_tren), Object.values(adv.kgb_tren), 'Jatuh Tempo KGB', '#f59e0b');
makeChart('chartPensiunProyeksi', 'bar', Object.keys(adv.pensiun_proyeksi).map(String), Object.values(adv.pensiun_proyeksi), 'Pensiun', ['#ef4444','#f97316','#f59e0b','#eab308','#84cc16']);
makeChart('chartPendidikan', 'doughnut', Object.keys(adv.pendidikan), Object.values(adv.pendidikan), 'Jumlah');
makeChart('chartJenisJabatan', 'doughnut', Object.keys(adv.jenis_jabatan), Object.values(adv.jenis_jabatan), 'Jumlah');
makeChart('chartMasaKerja', 'bar', Object.keys(adv.masa_kerja), Object.values(adv.masa_kerja), 'Jumlah', blueGradient);
</script>
@endpush

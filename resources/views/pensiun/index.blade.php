@extends('layouts.app')
@section('title', 'Pensiun')
@section('header', 'Alert Pensiun')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Pensiun</span>
@endsection
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="text-sm font-medium text-slate-700">Total: {{ count($alerts) }} pegawai</div>
        <div class="sm:ml-auto flex items-center gap-2">
            <input type="text" id="search-input" placeholder="Cari NIP/Nama..." class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
            <a href="{{ route('export', ['type' => 'pensiun', 'format' => 'pdf']) }}" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all">PDF</a>
            <a href="{{ route('export', ['type' => 'pensiun', 'format' => 'excel']) }}" class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all">Excel</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="data-table">
            <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jabatan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">BUP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Tgl Pensiun</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Sisa (Bulan)</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Level</th></tr></thead>
            <tbody class="divide-y divide-slate-100" id="table-body">
                @php $colors = ['Hitam' => 'bg-slate-800 text-white', 'Merah' => 'bg-red-100 text-red-700', 'Kuning' => 'bg-yellow-100 text-yellow-700', 'Hijau' => 'bg-green-100 text-green-700']; @endphp
                @forelse($alerts as $a)
                <tr class="hover:bg-slate-50 data-row"><td class="px-4 py-2.5 font-mono text-xs">{{ $a['nip'] }}</td><td class="px-4 py-2.5 font-medium text-slate-700"><a href="{{ route('pegawai.show', $a['pegawai_id']) }}" class="hover:text-blue-600">{{ $a['nama_lengkap'] }}</a></td><td class="px-4 py-2.5">{{ $a['jabatan_terakhir'] }}</td><td class="px-4 py-2.5">{{ $a['bup'] }}</td><td class="px-4 py-2.5">{{ $a['tanggal_pensiun']->format('d/m/Y') }}</td><td class="px-4 py-2.5">{{ $a['bulan_menuju_pensiun'] }}</td>
                <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $colors[$a['alert_level']] ?? '' }}">{{ $a['alert_level'] }}</span></td></tr>
                @empty <tr class="empty-row"><td colspan="7" class="px-4 py-8 text-center text-slate-400">Tidak ada data.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500" id="pagination-controls">
        <span id="page-info"></span>
        <div class="flex gap-1" id="page-buttons"></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function(){
    const perPage = 15;
    const rows = Array.from(document.querySelectorAll('.data-row'));
    const searchInput = document.getElementById('search-input');
    const pageInfo = document.getElementById('page-info');
    const pageButtons = document.getElementById('page-buttons');
    let currentPage = 1, filtered = rows;

    function filterRows() {
        const q = searchInput.value.toLowerCase();
        filtered = rows.filter(r => r.textContent.toLowerCase().includes(q));
        currentPage = 1;
        render();
    }
    function render() {
        const total = filtered.length, pages = Math.ceil(total / perPage) || 1;
        if (currentPage > pages) currentPage = pages;
        rows.forEach(r => r.style.display = 'none');
        filtered.slice((currentPage-1)*perPage, currentPage*perPage).forEach(r => r.style.display = '');
        pageInfo.textContent = `Menampilkan ${Math.min((currentPage-1)*perPage+1, total)}-${Math.min(currentPage*perPage, total)} dari ${total}`;
        pageButtons.innerHTML = '';
        for (let i = 1; i <= pages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            if (i === currentPage) {
                btn.style.cssText = 'padding:0.25rem 0.625rem;border-radius:0.5rem;font-size:0.75rem;background:#2563eb;color:#fff;cursor:default;';
            } else {
                btn.style.cssText = 'padding:0.25rem 0.625rem;border-radius:0.5rem;font-size:0.75rem;background:#f1f5f9;color:#475569;cursor:pointer;';
                btn.onmouseenter = function(){ this.style.background='#e2e8f0'; };
                btn.onmouseleave = function(){ this.style.background='#f1f5f9'; };
            }
            btn.onclick = () => { currentPage = i; render(); };
            pageButtons.appendChild(btn);
        }
    }
    searchInput.addEventListener('input', filterRows);
    render();
})();
</script>
@endpush

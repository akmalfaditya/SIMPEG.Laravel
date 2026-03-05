@extends('layouts.app')
@section('title', 'Satyalencana')
@section('header', 'Kandidat Satyalencana')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Satyalencana</span>
@endsection
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('satyalencana.index') }}" class="px-3 py-1.5 text-xs rounded-lg {{ !$selectedMilestone ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
            <a href="{{ route('satyalencana.index', ['milestone' => 10]) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $selectedMilestone == 10 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">10 Tahun</a>
            <a href="{{ route('satyalencana.index', ['milestone' => 20]) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $selectedMilestone == 20 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">20 Tahun</a>
            <a href="{{ route('satyalencana.index', ['milestone' => 30]) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $selectedMilestone == 30 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">30 Tahun</a>
        </div>
        <div class="sm:ml-auto flex items-center gap-2">
            <input type="text" id="search-input" placeholder="Cari NIP/Nama..." class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
            <a href="{{ route('export', ['type' => 'satyalencana', 'format' => 'pdf']) }}" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all">PDF</a>
            <a href="{{ route('export', ['type' => 'satyalencana', 'format' => 'excel']) }}" class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all">Excel</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="data-table">
            <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">NIP</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Nama</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Pangkat</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Jabatan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Masa Kerja</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Milestone</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Penghargaan</th><th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Aksi</th></tr></thead>
            <tbody class="divide-y divide-slate-100" id="table-body">
                @forelse($candidates as $c)
                <tr class="hover:bg-slate-50 data-row"><td class="px-4 py-2.5 font-mono text-xs">{{ $c['nip'] }}</td><td class="px-4 py-2.5 font-medium text-slate-700"><a href="{{ route('pegawai.show', $c['pegawai_id']) }}" class="hover:text-blue-600">{{ $c['nama_lengkap'] }}</a></td><td class="px-4 py-2.5">{{ $c['pangkat_terakhir'] }}</td><td class="px-4 py-2.5">{{ $c['jabatan_terakhir'] }}</td><td class="px-4 py-2.5">{{ $c['masa_kerja_tahun'] }} tahun</td>
                <td class="px-4 py-2.5"><span class="px-2 py-1 text-xs rounded-full font-medium {{ $c['milestone'] == 30 ? 'bg-amber-100 text-amber-700' : ($c['milestone'] == 20 ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">{{ $c['milestone'] }} Tahun</span></td>
                <td class="px-4 py-2.5 text-xs">{{ $c['nama_penghargaan'] }}</td>
                <td class="px-4 py-2.5">
                    <button onclick="openAwardModal({{ $c['pegawai_id'] }}, {{ $c['milestone'] }}, '{{ $c['nama_lengkap'] }}')" class="px-2 py-1 text-xs bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 transition-all">Tandai</button>
                </td></tr>
                @empty <tr class="empty-row"><td colspan="8" class="px-4 py-8 text-center text-slate-400">Tidak ada kandidat.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500" id="pagination-controls">
        <span id="page-info"></span>
        <div class="flex gap-1" id="page-buttons"></div>
    </div>
</div>

{{-- Award Modal --}}
<div id="award-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeAwardModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-slate-800 mb-1">Tandai Dianugerahkan</h3>
        <p class="text-sm text-slate-500 mb-4" id="award-modal-info"></p>
        <form method="POST" action="{{ route('satyalencana.award') }}">
            @csrf
            <input type="hidden" name="pegawai_id" id="award-pegawai-id">
            <input type="hidden" name="milestone" id="award-milestone">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nomor SK</label>
                    <input type="text" name="nomor_sk" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal SK</label>
                    <input type="date" name="tanggal_sk" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-5">
                <button type="button" onclick="closeAwardModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-300 hover:bg-slate-50 transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openAwardModal(pegawaiId, milestone, nama) {
    document.getElementById('award-modal').classList.remove('hidden');
    document.getElementById('award-pegawai-id').value = pegawaiId;
    document.getElementById('award-milestone').value = milestone;
    document.getElementById('award-modal-info').textContent = nama + ' — Milestone ' + milestone + ' Tahun';
}
function closeAwardModal() { document.getElementById('award-modal').classList.add('hidden'); }

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

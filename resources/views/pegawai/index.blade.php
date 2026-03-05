@extends('layouts.app')
@section('title', 'Data Pegawai')
@section('header', 'Data Pegawai')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <input type="text" id="searchInput" placeholder="Cari NIP, nama, atau unit kerja..."
                class="flex-1 max-w-sm px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
        </div>
        <a href="{{ route('pegawai.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md hover:scale-[1.02] transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pegawai
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="pegawaiTable">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">NIP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Lengkap</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pangkat</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Jabatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Masa Kerja</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody id="pegawaiBody" class="divide-y divide-slate-100"></tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 flex items-center justify-between" id="paginationRow">
        <p class="text-xs text-slate-500" id="paginationInfo"></p>
        <div class="flex gap-1" id="paginationBtns"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
const limit = 10;

function loadData() {
    const search = document.getElementById('searchInput').value;
    fetch(`{{ route('pegawai.data') }}?page=${currentPage}&limit=${limit}&search=${encodeURIComponent(search)}`)
        .then(r => r.json())
        .then(d => {
            const body = document.getElementById('pegawaiBody');
            body.innerHTML = '';
            if (!d.data.length) {
                body.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada data pegawai.</td></tr>';
            }
            d.data.forEach(r => {
                body.innerHTML += `<tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-slate-600">${r.nip}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">${r.nama_lengkap}</td>
                    <td class="px-4 py-3 text-slate-600">${r.pangkat_terakhir}</td>
                    <td class="px-4 py-3 text-slate-600">${r.jabatan_terakhir}</td>
                    <td class="px-4 py-3 text-slate-600">${r.masa_kerja}</td>
                    <td class="px-4 py-3"><a href="/pegawai/${r.id}" class="text-blue-600 hover:underline text-xs font-medium">Detail →</a></td>
                </tr>`;
            });
            const totalPages = Math.ceil(d.total / limit);
            document.getElementById('paginationInfo').textContent = `Halaman ${currentPage} dari ${totalPages} (${d.total} data)`;
            const btns = document.getElementById('paginationBtns');
            btns.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                btns.innerHTML += `<button onclick="currentPage=${i};loadData()" class="px-3 py-1.5 text-xs rounded-lg transition-all ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}">${i}</button>`;
            }
        });
}

document.getElementById('searchInput').addEventListener('input', function() { currentPage = 1; loadData(); });
loadData();
</script>
@endpush

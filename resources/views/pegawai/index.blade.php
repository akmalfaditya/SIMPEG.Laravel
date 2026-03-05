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
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="/pegawai/${r.id}" class="inline-flex items-center px-2 py-1 bg-slate-50 text-slate-600 hover:bg-slate-100 text-xs rounded-md font-medium transition-colors">Detail</a>
                            <a href="/pegawai/${r.id}/edit" class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                            <button type="button" onclick="confirmDelete('/pegawai/${r.id}', 'Yakin ingin menghapus data pegawai ${r.nama_lengkap}?')" class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                        </div>
                    </td>
                </tr>`;
            });
            const totalPages = Math.ceil(d.total / limit);
            document.getElementById('paginationInfo').textContent = `Halaman ${currentPage} dari ${totalPages} (${d.total} data)`;
            const btns = document.getElementById('paginationBtns');
            btns.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                const pg = i;
                btn.onclick = () => { currentPage = pg; loadData(); };
                if (i === currentPage) {
                    btn.style.cssText = 'padding:0.375rem 0.75rem;font-size:0.75rem;border-radius:0.5rem;background:#2563eb;color:#fff;cursor:default;';
                } else {
                    btn.style.cssText = 'padding:0.375rem 0.75rem;font-size:0.75rem;border-radius:0.5rem;background:#f1f5f9;color:#475569;cursor:pointer;';
                    btn.onmouseenter = function(){ this.style.background='#e2e8f0'; };
                    btn.onmouseleave = function(){ this.style.background='#f1f5f9'; };
                }
                btns.appendChild(btn);
            }
        });
}

document.getElementById('searchInput').addEventListener('input', function() { currentPage = 1; loadData(); });
loadData();
</script>
@endpush

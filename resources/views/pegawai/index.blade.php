@extends('layouts.app')
@section('title', request()->query('rumpun') ? 'Pegawai ' . request()->query('rumpun') : 'Data Pegawai')
@section('header', request()->query('rumpun') ? 'Data Pegawai ' . request()->query('rumpun') : 'Semua Pegawai')

@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
    {{-- Tabs --}}
    <div class="border-b border-slate-200">
        <nav class="flex -mb-px px-5 pt-3 gap-1">
            <button onclick="switchTab('aktif')" id="tab-aktif"
                class="tab-btn px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all">
                Aktif <span id="count-aktif" class="ml-1 text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full"></span>
            </button>
            <button onclick="switchTab('tidak-aktif')" id="tab-tidak-aktif"
                class="tab-btn px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all">
                Tidak Aktif <span id="count-tidak-aktif" class="ml-1 text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full"></span>
            </button>
            <button onclick="switchTab('pensiun')" id="tab-pensiun"
                class="tab-btn px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all">
                Pensiun <span id="count-pensiun" class="ml-1 text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full"></span>
            </button>
        </nav>
    </div>

    {{-- Toolbar --}}
    <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <input type="text" id="searchInput" placeholder="Cari NIP, nama, atau unit kerja..."
                class="flex-1 max-w-sm px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800 transition-all">
        </div>
        <a href="{{ route('pegawai.create') }}" class="px-4 py-2.5 bg-blue-800 hover:bg-blue-900 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pegawai
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="pegawaiTable">
            <thead class="bg-slate-50">
                <tr id="tableHead">
                    {{-- Populated by JS per tab --}}
                </tr>
            </thead>
            <tbody id="pegawaiBody" class="divide-y divide-slate-100"></tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t border-slate-100 flex items-center justify-between" id="paginationRow">
        <p class="text-xs text-slate-500" id="paginationInfo"></p>
        <div class="flex gap-1" id="paginationBtns"></div>
    </div>
</div>

{{-- Confirmation Dialog for PATCH actions (reactivate / cancel-pensiun) --}}
<dialog id="patch-modal" class="rounded-xl shadow-xl p-6 w-full max-w-sm backdrop:bg-black/50 m-auto fixed inset-0">
    <h3 class="text-lg font-semibold text-slate-800 mb-2" id="patch-modal-title">Konfirmasi</h3>
    <p class="text-sm text-slate-600 mb-5" id="patch-modal-message"></p>
    <div class="flex gap-3 justify-end">
        <button onclick="closePatchModal()"
            class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-300 hover:bg-slate-50 transition-all">Batal</button>
        <form id="patch-modal-form" method="POST">
            @csrf @method('PATCH')
            <button type="submit" id="patch-modal-submit"
                class="px-4 py-2 text-sm text-white rounded-lg transition-all">Konfirmasi</button>
        </form>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let activeTab = 'aktif';
const limit = 10;
const rumpunFilter = new URLSearchParams(window.location.search).get('rumpun') || '';

const headConfigs = {
    'aktif': `
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">NIP</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Nama Lengkap</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Pangkat</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Jabatan</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Masa Kerja</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10 sticky right-0">Aksi</th>`,
    'tidak-aktif': `
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">NIP</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Nama Lengkap</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Pangkat</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Jabatan</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Masa Kerja</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10 sticky right-0">Aksi</th>`,
    'pensiun': `
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">NIP</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Nama Lengkap</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Pangkat</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">SK Pensiun</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">TMT Pensiun</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10">Dokumen SK</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide sticky top-0 bg-slate-50 z-10 sticky right-0">Aksi</th>`,
};

function switchTab(tab) {
    activeTab = tab;
    currentPage = 1;

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-800', 'text-blue-800');
        btn.classList.add('border-transparent', 'text-slate-500', 'hover:text-slate-700');
    });
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.classList.add('border-blue-800', 'text-blue-800');
    activeBtn.classList.remove('border-transparent', 'text-slate-500', 'hover:text-slate-700');

    document.getElementById('tableHead').innerHTML = headConfigs[tab];
    loadData();
}

function renderRow(r) {
    const hukdisBadge = r.has_active_hukdis
        ? ' <span class="ml-1 inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold rounded-full bg-rose-100 text-rose-700">Hukdis</span>'
        : '';

    const cols = {
        nip: `<td class="px-4 py-3 font-mono text-xs text-slate-600">${r.nip}</td>`,
        nama: `<td class="px-4 py-3 font-medium text-slate-800">${r.nama_lengkap}${hukdisBadge}</td>`,
        pangkat: `<td class="px-4 py-3 text-slate-600">${r.pangkat_terakhir}</td>`,
        jabatan: `<td class="px-4 py-3 text-slate-600">${r.jabatan_terakhir}</td>`,
        masaKerja: `<td class="px-4 py-3 text-slate-600">${r.masa_kerja}</td>`,
        skPensiun: `<td class="px-4 py-3 text-slate-600">${r.sk_pensiun_nomor ?? '-'}</td>`,
        tmtPensiun: `<td class="px-4 py-3 text-slate-600">${r.tmt_pensiun ?? '-'}</td>`,
        dokumenSk: (() => {
            let buttons = [];
            if (r.file_sk_pensiun_path) {
                buttons.push(`<a href="/dokumen/pensiun/${r.id}" target="_blank" class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Lihat PDF</a>`);
            }
            if (r.link_sk_pensiun_gdrive) {
                buttons.push(`<a href="${r.link_sk_pensiun_gdrive}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-2 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-xs rounded-md font-medium transition-colors">Google Drive</a>`);
            }
            if (!buttons.length) {
                buttons.push(`<span class="text-xs text-slate-400">Tidak ada dokumen</span>`);
            }
            return `<td class="px-4 py-3"><div class="flex items-center gap-2">${buttons.join('')}</div></td>`;
        })(),
    };

    let actions = '';
    if (activeTab === 'aktif') {
        actions = `
            <a href="/pegawai/${r.id}" class="inline-flex items-center px-2 py-1 bg-slate-50 text-slate-600 hover:bg-slate-100 text-xs rounded-md font-medium transition-colors">Detail</a>
            <a href="/pegawai/${r.id}/edit" class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
            <button type="button" onclick="confirmDelete('/pegawai/${r.id}', 'Yakin ingin menghapus data pegawai ${r.nama_lengkap}?')" class="inline-flex items-center px-2 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs rounded-md font-medium transition-colors">Hapus</button>`;
    } else if (activeTab === 'tidak-aktif') {
        actions = `
            <a href="/pegawai/${r.id}" class="inline-flex items-center px-2 py-1 bg-slate-50 text-slate-600 hover:bg-slate-100 text-xs rounded-md font-medium transition-colors">Detail</a>
            <button type="button" onclick="confirmPatch('/pegawai/${r.id}/reactivate', 'Aktifkan Kembali', 'Yakin ingin mengaktifkan kembali pegawai ${r.nama_lengkap}?', 'bg-emerald-600 hover:bg-emerald-700')" class="inline-flex items-center px-2 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-xs rounded-md font-medium transition-colors">Aktifkan Kembali</button>`;
    } else if (activeTab === 'pensiun') {
        actions = `
            <a href="/pegawai/${r.id}" class="inline-flex items-center px-2 py-1 bg-slate-50 text-slate-600 hover:bg-slate-100 text-xs rounded-md font-medium transition-colors">Detail</a>
            <button type="button" onclick="confirmPatch('/pegawai/${r.id}/cancel-pensiun', 'Batalkan Pensiun', 'Yakin ingin membatalkan pensiun pegawai ${r.nama_lengkap}? Semua data SK pensiun akan dihapus.', 'bg-amber-600 hover:bg-amber-700')" class="inline-flex items-center px-2 py-1 bg-amber-50 text-amber-600 hover:bg-amber-100 text-xs rounded-md font-medium transition-colors">Batalkan Pensiun</button>`;
    }

    const actionTd = `<td class="px-4 py-3"><div class="flex items-center gap-2">${actions}</div></td>`;

    if (activeTab === 'pensiun') {
        return `<tr class="hover:bg-slate-50 transition-colors">${cols.nip}${cols.nama}${cols.pangkat}${cols.skPensiun}${cols.tmtPensiun}${cols.dokumenSk}${actionTd}</tr>`;
    }
    return `<tr class="hover:bg-slate-50 transition-colors">${cols.nip}${cols.nama}${cols.pangkat}${cols.jabatan}${cols.masaKerja}${actionTd}</tr>`;
}

function loadData() {
    const search = document.getElementById('searchInput').value;
    let url = `{{ route('pegawai.data') }}?page=${currentPage}&limit=${limit}&search=${encodeURIComponent(search)}&status=${activeTab}`;
    if (rumpunFilter) {
        url += `&rumpun=${encodeURIComponent(rumpunFilter)}`;
    }
    fetch(url)
        .then(r => r.json())
        .then(d => {
            const body = document.getElementById('pegawaiBody');
            body.innerHTML = '';

            // Update tab count
            document.getElementById('count-' + activeTab).textContent = d.total;

            if (!d.data.length) {
                const colCount = activeTab === 'pensiun' ? 7 : 6;
                body.innerHTML = `<tr><td colspan="${colCount}" class="px-4 py-8 text-center text-slate-400">Tidak ada data pegawai.</td></tr>`;
            }
            d.data.forEach(r => { body.innerHTML += renderRow(r); });

            const totalPages = d.last_page;
            document.getElementById('paginationInfo').textContent = `Halaman ${d.current_page} dari ${totalPages} (${d.total} data)`;
            const btns = document.getElementById('paginationBtns');
            btns.innerHTML = '';

            // Build smart page range: first, last, and window around current
            let pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                pages.push(1);
                let start = Math.max(2, currentPage - 1);
                let end = Math.min(totalPages - 1, currentPage + 1);
                if (start > 2) pages.push('...');
                for (let i = start; i <= end; i++) pages.push(i);
                if (end < totalPages - 1) pages.push('...');
                pages.push(totalPages);
            }

            pages.forEach(p => {
                if (p === '...') {
                    const span = document.createElement('span');
                    span.textContent = '…';
                    span.style.cssText = 'padding:0.375rem 0.5rem;font-size:0.75rem;color:#94a3b8;';
                    btns.appendChild(span);
                    return;
                }
                const btn = document.createElement('button');
                btn.textContent = p;
                const pg = p;
                btn.onclick = () => { currentPage = pg; loadData(); };
                if (p === currentPage) {
                    btn.style.cssText = 'padding:0.375rem 0.75rem;font-size:0.75rem;border-radius:0.5rem;background:#1e40af;color:#fff;cursor:default;';
                } else {
                    btn.style.cssText = 'padding:0.375rem 0.75rem;font-size:0.75rem;border-radius:0.5rem;background:#f1f5f9;color:#475569;cursor:pointer;';
                    btn.onmouseenter = function(){ this.style.background='#e2e8f0'; };
                    btn.onmouseleave = function(){ this.style.background='#f1f5f9'; };
                }
                btns.appendChild(btn);
            });
        });
}

// Load initial counts for all tabs
function loadTabCounts() {
    ['aktif', 'tidak-aktif', 'pensiun'].forEach(tab => {
        let url = `{{ route('pegawai.data') }}?page=1&limit=1&status=${tab}`;
        if (rumpunFilter) {
            url += `&rumpun=${encodeURIComponent(rumpunFilter)}`;
        }
        fetch(url)
            .then(r => r.json())
            .then(d => {
                document.getElementById('count-' + tab).textContent = d.total;
            });
    });
}

// PATCH confirmation dialog (HTML5 <dialog>)
function confirmPatch(url, title, message, btnClass) {
    const dialog = document.getElementById('patch-modal');
    document.getElementById('patch-modal-form').action = url;
    document.getElementById('patch-modal-title').textContent = title;
    document.getElementById('patch-modal-message').textContent = message;
    const submitBtn = document.getElementById('patch-modal-submit');
    submitBtn.className = 'px-4 py-2 text-sm text-white rounded-lg transition-all ' + btnClass;
    dialog.showModal();
}

function closePatchModal() {
    document.getElementById('patch-modal').close();
}

document.getElementById('patch-modal').addEventListener('click', function(e) {
    if (e.target === this) this.close();
});

let searchTimer;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { currentPage = 1; loadData(); }, 300);
});
switchTab('aktif');
loadTabCounts();
</script>
@endpush

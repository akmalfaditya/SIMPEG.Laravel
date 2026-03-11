@extends('layouts.app')
@section('title', 'Data Pegawai')
@section('header', 'Data Pegawai')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
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
                class="flex-1 max-w-sm px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
        </div>
        <a href="{{ route('pegawai.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md hover:scale-[1.02] transition-all flex items-center gap-2">
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

{{-- Confirmation Modal for PATCH actions (reactivate / cancel-pensiun) --}}
<div id="patch-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closePatchModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
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
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let activeTab = 'aktif';
const limit = 10;

const headConfigs = {
    'aktif': `
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">NIP</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Lengkap</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pangkat</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Jabatan</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Masa Kerja</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>`,
    'tidak-aktif': `
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">NIP</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Lengkap</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pangkat</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Jabatan</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Masa Kerja</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>`,
    'pensiun': `
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">NIP</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Lengkap</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pangkat</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">SK Pensiun</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">TMT Pensiun</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Dokumen SK</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>`,
};

function switchTab(tab) {
    activeTab = tab;
    currentPage = 1;

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-slate-500', 'hover:text-slate-700');
    });
    const activeBtn = document.getElementById('tab-' + tab);
    activeBtn.classList.add('border-blue-500', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-slate-500', 'hover:text-slate-700');

    document.getElementById('tableHead').innerHTML = headConfigs[tab];
    loadData();
}

function renderRow(r) {
    const hukdisBadge = r.has_active_hukdis
        ? ' <span class="ml-1 inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold rounded-full bg-red-100 text-red-700">Hukdis</span>'
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
            <button type="button" onclick="confirmDelete('/pegawai/${r.id}', 'Yakin ingin menghapus data pegawai ${r.nama_lengkap}?')" class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>`;
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
    fetch(`{{ route('pegawai.data') }}?page=${currentPage}&limit=${limit}&search=${encodeURIComponent(search)}&status=${activeTab}`)
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
                    btn.style.cssText = 'padding:0.375rem 0.75rem;font-size:0.75rem;border-radius:0.5rem;background:#2563eb;color:#fff;cursor:default;';
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
        fetch(`{{ route('pegawai.data') }}?page=1&limit=1&status=${tab}`)
            .then(r => r.json())
            .then(d => {
                document.getElementById('count-' + tab).textContent = d.total;
            });
    });
}

// PATCH confirmation modal
function confirmPatch(url, title, message, btnClass) {
    document.getElementById('patch-modal').classList.remove('hidden');
    document.getElementById('patch-modal-form').action = url;
    document.getElementById('patch-modal-title').textContent = title;
    document.getElementById('patch-modal-message').textContent = message;
    const submitBtn = document.getElementById('patch-modal-submit');
    submitBtn.className = 'px-4 py-2 text-sm text-white rounded-lg transition-all ' + btnClass;
}

function closePatchModal() {
    document.getElementById('patch-modal').classList.add('hidden');
}

let searchTimer;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { currentPage = 1; loadData(); }, 300);
});
switchTab('aktif');
loadTabCounts();
</script>
@endpush

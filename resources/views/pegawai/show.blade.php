@extends('layouts.app')
@section('title', 'Detail Pegawai')
@section('header', 'Detail Pegawai')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a href="{{ route('pegawai.index') }}"
        class="hover:text-blue-600">Pegawai</a> / <span class="text-slate-700">{{ $pegawai->nama_lengkap }}</span>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Header Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">
                        {{ $pegawai->nama_lengkap }}
                        @if ($pegawai->statusKepegawaian)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                                {{ $pegawai->statusKepegawaian->nama === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : ($pegawai->statusKepegawaian->nama === 'Pensiun' ? 'bg-slate-100 text-slate-600' : 'bg-red-100 text-red-700') }}">{{ $pegawai->statusKepegawaian->nama }}</span>
                        @endif
                        @if ($pegawai->has_active_hukdis)
                            <span
                                class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Hukdis
                                Aktif</span>
                        @endif
                    </h3>
                    <p class="text-sm text-slate-500 font-mono mt-1">NIP: {{ $pegawai->nip }}</p>
                    <div class="flex gap-4 mt-3 text-sm text-slate-600 flex-wrap">
                        <span><strong>Golongan:</strong> {{ $pegawai->pangkat_terakhir ?? '-' }}</span>
                        <span><strong>Jabatan:</strong> {{ $pegawai->jabatan_terakhir ?? '-' }}</span>
                        <span><strong>Masa Kerja:</strong> {{ $pegawai->masa_kerja }}</span>
                        <span><strong>Unit Kerja:</strong> {{ $pegawai->unitKerja?->nama ?? '-' }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('pegawai.export-pdf', $pegawai) }}"
                        class="px-4 py-2 bg-slate-600 text-white text-sm rounded-lg hover:bg-slate-700 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF</a>
                    <a href="{{ route('pegawai.edit', $pegawai) }}"
                        class="px-4 py-2 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 transition-colors">Edit</a>
                    <button
                        onclick="confirmDelete('{{ route('pegawai.destroy', $pegawai) }}', 'Yakin ingin menghapus data pegawai {{ $pegawai->nama_lengkap }}? Tindakan ini tidak dapat dibatalkan.')"
                        class="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                </div>
            </div>
        </div>

        {{-- Data Pribadi --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-sm font-semibold text-slate-700 mb-4">Data Pribadi</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-slate-400 text-xs">Tempat Lahir</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->tempat_lahir ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Tanggal Lahir</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->tanggal_lahir->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Jenis Kelamin</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->jenisKelamin?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Agama</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->agama?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Status Pernikahan</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->statusPernikahan?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Golongan Darah</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->golonganDarah?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Email</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">No Telepon</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->no_telepon ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-slate-400 text-xs">Alamat</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Data Kepegawaian --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-sm font-semibold text-slate-700 mb-4">Data Kepegawaian</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-slate-400 text-xs">Tipe Pegawai</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->tipePegawai?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Bagian</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->bagian?->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">TMT CPNS</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->tmt_cpns->format('d/m/Y') }}</p>
                    @if($pegawai->sk_cpns_path)
                        <a href="{{ route('dokumen.download', ['type' => 'sk_cpns', 'id' => $pegawai->id]) }}" target="_blank"
                            class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Lihat SK CPNS
                        </a>
                    @endif
                </div>
                <div>
                    <p class="text-slate-400 text-xs">TMT PNS</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->tmt_pns?->format('d/m/Y') ?? '-' }}</p>
                    @if($pegawai->sk_pns_path)
                        <a href="{{ route('dokumen.download', ['type' => 'sk_pns', 'id' => $pegawai->id]) }}" target="_blank"
                            class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Lihat SK PNS
                        </a>
                    @endif
                </div>
                <div>
                    <p class="text-slate-400 text-xs">Gaji Pokok</p>
                    <p class="text-slate-700 font-medium">Rp {{ number_format($pegawai->gaji_pokok, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs">NPWP</p>
                    <p class="text-slate-700 font-medium">{{ $pegawai->npwp ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Data Completeness Indicator --}}
        @php
            $completeness = [
                'pangkat' => ['label' => 'Pangkat', 'filled' => $pegawai->riwayatPangkat->isNotEmpty()],
                'jabatan' => ['label' => 'Jabatan', 'filled' => $pegawai->riwayatJabatan->isNotEmpty()],
                'kgb' => ['label' => 'KGB', 'filled' => $pegawai->riwayatKgb->isNotEmpty()],
                'hukuman' => ['label' => 'Hukuman', 'filled' => $pegawai->riwayatHukumanDisiplin->isNotEmpty()],
                'pendidikan' => ['label' => 'Pendidikan', 'filled' => $pegawai->riwayatPendidikan->isNotEmpty()],
                'latihan' => ['label' => 'Latihan', 'filled' => $pegawai->riwayatLatihanJabatan->isNotEmpty()],
                'skp' => ['label' => 'SKP', 'filled' => $pegawai->penilaianKinerja->isNotEmpty()],
                'penghargaan' => ['label' => 'Penghargaan', 'filled' => $pegawai->riwayatPenghargaan->isNotEmpty()],
            ];
            $filledCount = collect($completeness)->where('filled', true)->count();
            $totalCount = count($completeness);
            $percentage = round(($filledCount / $totalCount) * 100);
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-slate-700">Kelengkapan Data:</span>
                    <span class="text-sm font-bold {{ $percentage === 100 ? 'text-emerald-600' : ($percentage >= 50 ? 'text-blue-600' : 'text-amber-600') }}">{{ $filledCount }}/{{ $totalCount }} riwayat terisi</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach($completeness as $key => $item)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full {{ $item['filled'] ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-600' }}">
                            @if($item['filled'])
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            @endif
                            {{ $item['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full transition-all {{ $percentage === 100 ? 'bg-emerald-500' : ($percentage >= 50 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width: {{ $percentage }}%"></div>
            </div>
        </div>

        {{-- Tabs for Riwayat --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex border-b border-slate-200 overflow-x-auto" id="tabNav">
                @foreach (['timeline' => 'Timeline Karir', 'pangkat' => 'Pangkat', 'jabatan' => 'Jabatan', 'kgb' => 'KGB', 'hukuman' => 'Hukuman', 'pendidikan' => 'Pendidikan', 'latihan' => 'Latihan', 'skp' => 'SKP', 'penghargaan' => 'Penghargaan'] as $key => $label)
                    <button onclick="showTab('{{ $key }}')"
                        class="tab-btn px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2 {{ $loop->first ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} flex items-center gap-1"
                        data-tab="{{ $key }}">{{ $label }}@if($key !== 'timeline' && !($completeness[$key]['filled'] ?? true))<span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-400" title="Belum ada data"></span>@endif</button>
                @endforeach
            </div>

            {{-- Timeline Karir --}}
            <div class="tab-content p-5" id="tab-timeline">
                @if(count($timeline) > 0)
                <div class="relative">
                    {{-- Vertical line --}}
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>

                    <div class="space-y-4">
                        @php $currentYear = null; @endphp
                        @foreach($timeline as $item)
                            @php
                                $itemYear = \Carbon\Carbon::parse($item['date'])->year;
                                $showYear = $itemYear !== $currentYear;
                                $currentYear = $itemYear;
                                $colorMap = [
                                    'blue' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50 border-blue-200 text-blue-700', 'badge' => 'bg-blue-100 text-blue-700'],
                                    'indigo' => ['bg' => 'bg-indigo-500', 'light' => 'bg-indigo-50 border-indigo-200 text-indigo-700', 'badge' => 'bg-indigo-100 text-indigo-700'],
                                    'emerald' => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'badge' => 'bg-emerald-100 text-emerald-700'],
                                    'red' => ['bg' => 'bg-red-500', 'light' => 'bg-red-50 border-red-200 text-red-700', 'badge' => 'bg-red-100 text-red-700'],
                                    'purple' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50 border-purple-200 text-purple-700', 'badge' => 'bg-purple-100 text-purple-700'],
                                    'cyan' => ['bg' => 'bg-cyan-500', 'light' => 'bg-cyan-50 border-cyan-200 text-cyan-700', 'badge' => 'bg-cyan-100 text-cyan-700'],
                                    'amber' => ['bg' => 'bg-amber-500', 'light' => 'bg-amber-50 border-amber-200 text-amber-700', 'badge' => 'bg-amber-100 text-amber-700'],
                                    'yellow' => ['bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50 border-yellow-200 text-yellow-700', 'badge' => 'bg-yellow-100 text-yellow-700'],
                                ];
                                $c = $colorMap[$item['color']] ?? $colorMap['blue'];
                            @endphp

                            @if($showYear)
                                <div class="relative flex items-center pl-10 pt-2">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $currentYear }}</span>
                                </div>
                            @endif

                            <div class="relative flex items-start gap-4 pl-10">
                                {{-- Dot on the line --}}
                                <div class="absolute left-2.5 top-1.5 w-3 h-3 rounded-full ring-2 ring-white {{ $c['bg'] }}"></div>

                                {{-- Card --}}
                                <div class="flex-1 border rounded-lg p-3 {{ $c['light'] }}">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $c['badge'] }}">{{ ucfirst($item['type']) }}</span>
                                            <span class="text-sm font-semibold">{{ $item['title'] }}</span>
                                        </div>
                                        <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</span>
                                    </div>
                                    <p class="text-sm mt-1">{{ $item['subtitle'] }}</p>
                                    @if($item['detail'])
                                        <p class="text-xs mt-0.5 opacity-75">{{ $item['detail'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                    <p class="text-center text-slate-400 py-8">Belum ada data riwayat untuk ditampilkan.</p>
                @endif
            </div>

            {{-- Pangkat --}}
            <div class="tab-content p-5 hidden" id="tab-pangkat">
                @php
                    $latestJabatan = $pegawai->riwayatJabatan->sortByDesc('tmt_jabatan')->first();
                    $isPPPK = $latestJabatan?->jabatan?->rumpunJabatan?->nama === 'PPPK';
                @endphp
                @if($isPPPK)
                <div class="mb-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"></path></svg>
                    <p class="text-xs text-blue-700">Pegawai PPPK tidak memiliki skema Kenaikan Pangkat sesuai ketentuan BKN. Kenaikan pangkat hanya berlaku untuk PNS (Struktural, JFT, JFU).</p>
                </div>
                @else
                <div class="flex justify-end mb-3"><a href="{{ route('riwayat.pangkat.create', $pegawai->id) }}"
                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah</a></div>
                @endif
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Golongan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">No SK</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">TMT</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Tgl SK</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Dokumen</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->riwayatPangkat->sortByDesc('tmt_pangkat') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $r->golongan?->label }}</td>
                                <td class="px-3 py-2">{{ $r->nomor_sk }}</td>
                                <td class="px-3 py-2">{{ $r->tmt_pangkat->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">{{ $r->tanggal_sk->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->file_pdf_path)
                                        <a href="{{ route('dokumen.download', ['type' => 'pangkat', 'id' => $r->id]) }}"
                                            target="_blank" class="text-blue-600 hover:underline text-xs">Lihat PDF</a>
                                    @elseif($r->google_drive_link)
                                        <a href="{{ $r->google_drive_link }}" target="_blank" rel="noopener noreferrer"
                                            class="text-blue-600 hover:underline text-xs">Drive</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('riwayat.pangkat.edit', $r) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('riwayat.pangkat.destroy', $r) }}', 'Hapus data riwayat pangkat ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Jabatan --}}
            <div class="tab-content p-5 hidden" id="tab-jabatan">
                <div class="flex justify-end mb-3"><a href="{{ route('riwayat.jabatan.create', $pegawai->id) }}"
                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah</a></div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Jabatan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">No SK</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">TMT</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Dokumen</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->riwayatJabatan->sortByDesc('tmt_jabatan') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $r->jabatan->nama_jabatan ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $r->nomor_sk }}</td>
                                <td class="px-3 py-2">{{ $r->tmt_jabatan->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->file_pdf_path)
                                        <a href="{{ route('dokumen.download', ['type' => 'jabatan', 'id' => $r->id]) }}"
                                            target="_blank" class="text-blue-600 hover:underline text-xs">Lihat PDF</a>
                                    @elseif($r->google_drive_link)
                                        <a href="{{ $r->google_drive_link }}" target="_blank" rel="noopener noreferrer"
                                            class="text-blue-600 hover:underline text-xs">Drive</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('riwayat.jabatan.edit', $r) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('riwayat.jabatan.destroy', $r) }}', 'Hapus data riwayat jabatan ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- KGB --}}
            <div class="tab-content p-5 hidden" id="tab-kgb">
                <div class="flex items-center justify-between mb-3">
                    @if($estimasiKgbSelanjutnya)
                    <div class="text-sm text-slate-600">
                        Estimasi KGB Selanjutnya: <span class="font-semibold text-blue-600">{{ $estimasiKgbSelanjutnya->format('d/m/Y') }}</span>
                    </div>
                    @else
                    <div></div>
                    @endif
                    <a href="{{ route('riwayat.kgb.create', $pegawai->id) }}"
                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah</a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">No SK</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">TMT</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Gaji Lama</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Gaji Baru</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Dokumen</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->riwayatKgb->sortByDesc('tmt_kgb') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $r->nomor_sk }}</td>
                                <td class="px-3 py-2">{{ $r->tmt_kgb->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($r->gaji_lama, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($r->gaji_baru, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->file_pdf_path)
                                        <a href="{{ route('dokumen.download', ['type' => 'kgb', 'id' => $r->id]) }}"
                                            target="_blank" class="text-blue-600 hover:underline text-xs">Lihat PDF</a>
                                    @elseif($r->google_drive_link)
                                        <a href="{{ $r->google_drive_link }}" target="_blank" rel="noopener noreferrer"
                                            class="text-blue-600 hover:underline text-xs">Drive</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('riwayat.kgb.edit', $r) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('riwayat.kgb.destroy', $r) }}', 'Hapus data riwayat KGB ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Hukuman --}}
            <div class="tab-content p-5 hidden" id="tab-hukuman">
                <div class="flex justify-end mb-3"><a href="{{ route('riwayat.hukuman.create', $pegawai->id) }}"
                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah</a></div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Tingkat</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Jenis Sanksi</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Durasi</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">TMT</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Dokumen</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->riwayatHukumanDisiplin->sortByDesc('tmt_hukuman') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full {{ $r->tingkat_hukuman == \App\Enums\TingkatHukuman::Berat ? 'bg-red-100 text-red-700' : ($r->tingkat_hukuman == \App\Enums\TingkatHukuman::Sedang ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">{{ $r->tingkat_hukuman->label() }}</span>
                                </td>
                                <td class="px-3 py-2">{{ $r->jenis_sanksi->label() }}</td>
                                <td class="px-3 py-2">{{ $r->durasi_tahun ? $r->durasi_tahun . ' thn' : '-' }}</td>
                                <td class="px-3 py-2">{{ $r->tmt_hukuman->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full {{ $r->status?->color() ?? 'bg-red-100 text-red-700' }}">{{ $r->status?->label() ?? 'Aktif' }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    @if ($r->file_pdf_path)
                                        <a href="{{ route('dokumen.download', ['type' => 'hukuman', 'id' => $r->id]) }}"
                                            target="_blank" class="text-blue-600 hover:underline text-xs">PDF</a>
                                    @elseif($r->google_drive_link)
                                        <a href="{{ $r->google_drive_link }}" target="_blank" rel="noopener noreferrer"
                                            class="text-blue-600 hover:underline text-xs">Drive</a>
                                    @else
                                        <span class="text-amber-600 text-xs font-medium">SK Belum Diunggah</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <a href="{{ route('riwayat.hukuman.edit', $r) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                        @if ($r->isAktif())
                                            <button type="button"
                                                onclick="openPulihkanModal({{ $r->id }}, {{ $r->jenis_sanksi->value }})"
                                                class="inline-flex items-center px-2 py-1 text-xs rounded-md font-medium transition-colors"
                                                style="background-color:#f0fdf4;color:#16a34a;">Pulihkan</button>
                                        @endif
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('riwayat.hukuman.destroy', $r) }}', 'Hapus data riwayat hukuman ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pendidikan --}}
            <div class="tab-content p-5 hidden" id="tab-pendidikan">
                <div class="flex justify-end mb-3"><a href="{{ route('riwayat.pendidikan.create', $pegawai->id) }}"
                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah</a></div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Tingkat</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Institusi</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Jurusan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Lulus</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Dokumen</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->riwayatPendidikan->sortByDesc('tahun_lulus') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $r->tingkat_pendidikan }}</td>
                                <td class="px-3 py-2">{{ $r->institusi }}</td>
                                <td class="px-3 py-2">{{ $r->jurusan }}</td>
                                <td class="px-3 py-2">{{ $r->tahun_lulus }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->file_pdf_path)
                                        <a href="{{ route('dokumen.download', ['type' => 'pendidikan', 'id' => $r->id]) }}"
                                            target="_blank" class="text-blue-600 hover:underline text-xs">Lihat PDF</a>
                                    @elseif($r->google_drive_link)
                                        <a href="{{ $r->google_drive_link }}" target="_blank" rel="noopener noreferrer"
                                            class="text-blue-600 hover:underline text-xs">Drive</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('riwayat.pendidikan.edit', $r) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('riwayat.pendidikan.destroy', $r) }}', 'Hapus data riwayat pendidikan ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Latihan --}}
            <div class="tab-content p-5 hidden" id="tab-latihan">
                <div class="flex justify-end mb-3"><a href="{{ route('riwayat.latihan.create', $pegawai->id) }}"
                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah</a></div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Nama Latihan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Tahun</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Jam</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Penyelenggara</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Dokumen</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->riwayatLatihanJabatan->sortByDesc('tahun_pelaksanaan') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $r->nama_latihan }}</td>
                                <td class="px-3 py-2">{{ $r->tahun_pelaksanaan }}</td>
                                <td class="px-3 py-2">{{ $r->jumlah_jam }}</td>
                                <td class="px-3 py-2">{{ $r->penyelenggara }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->file_pdf_path)
                                        <a href="{{ route('dokumen.download', ['type' => 'latihan', 'id' => $r->id]) }}"
                                            target="_blank" class="text-blue-600 hover:underline text-xs">Lihat PDF</a>
                                    @elseif($r->google_drive_link)
                                        <a href="{{ $r->google_drive_link }}" target="_blank" rel="noopener noreferrer"
                                            class="text-blue-600 hover:underline text-xs">Drive</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('riwayat.latihan.edit', $r) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('riwayat.latihan.destroy', $r) }}', 'Hapus data riwayat latihan ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Penghargaan --}}
            <div class="tab-content p-5 hidden" id="tab-penghargaan">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Nama Penghargaan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Tahun</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Milestone</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">No SK</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Tgl SK</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->riwayatPenghargaan->sortByDesc('tahun') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $r->nama_penghargaan }}</td>
                                <td class="px-3 py-2">{{ $r->tahun }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->milestone)
                                        <span
                                            class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700">{{ $r->milestone }}
                                            Tahun</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $r->nomor_sk ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $r->tanggal_sk?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- SKP --}}
            <div class="tab-content p-5 hidden" id="tab-skp">
                <div class="flex justify-end mb-3"><a href="{{ route('riwayat.skp.create', $pegawai->id) }}"
                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">+ Tambah</a></div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Tahun</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Nilai SKP</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500">Dokumen</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai->penilaianKinerja->sortByDesc('tahun') as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $r->tahun }}</td>
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full {{ $r->nilai_skp === 'Sangat Baik' ? 'bg-emerald-100 text-emerald-700' : ($r->nilai_skp === 'Baik' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">{{ $r->nilai_skp }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    @if ($r->file_pdf_path)
                                        <a href="{{ route('dokumen.download', ['type' => 'skp', 'id' => $r->id]) }}"
                                            target="_blank" class="text-blue-600 hover:underline text-xs">Lihat PDF</a>
                                    @elseif($r->google_drive_link)
                                        <a href="{{ $r->google_drive_link }}" target="_blank" rel="noopener noreferrer"
                                            class="text-blue-600 hover:underline text-xs">Drive</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('riwayat.skp.edit', $r) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('riwayat.skp.destroy', $r) }}', 'Hapus data SKP ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-slate-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Pemulihan Modal --}}
    <div id="pulihkanModal" style="display:none;position:fixed;inset:0;z-index:50;background:rgba(0,0,0,0.4);">
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:100%;max-width:32rem;"
            class="bg-white rounded-2xl shadow-xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Pemulihan Hukuman Disiplin</h3>
            <form id="pulihkanForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK Pemulihan *</label>
                        <input type="text" name="nomor_sk_pemulihan" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pemulihan *</label>
                        <input type="date" name="tanggal_pemulihan" required value="{{ today()->format('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Upload SK Pemulihan (PDF)</label>
                        <input type="file" name="file_sk_pemulihan" accept=".pdf"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
                    </div>
                    {{-- Restoration: Penurunan Pangkat --}}
                    <div id="restorationPangkatSection" style="display:none;"
                        class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs font-semibold text-green-700 mb-2">Pemulihan Pangkat — Pilih pangkat yang
                            dikembalikan:</p>
                        <select name="restoration_golongan_id"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="">-- Pilih Golongan Ruang --</option>
                            @foreach ($golonganOptions as $g)
                                <option value="{{ $g->id }}">{{ $g->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Restoration: Penurunan/Pembebasan Jabatan --}}
                    <div id="restorationJabatanSection" style="display:none;"
                        class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs font-semibold text-green-700 mb-2">Pemulihan Jabatan — Pilih jabatan yang
                            dikembalikan:</p>
                        <select name="restoration_jabatan_id"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatanOptions as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 mt-5 pt-4 border-t border-slate-200">
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700">Pulihkan</button>
                    <button type="button" onclick="closePulihkanModal()"
                        class="px-5 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showTab(name) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-blue-600', 'text-blue-600');
                el.classList.add('border-transparent', 'text-slate-500');
            });
            const panel = document.getElementById('tab-' + name);
            const btn = document.querySelector(`.tab-btn[data-tab="${name}"]`);
            if (panel && btn) {
                panel.classList.remove('hidden');
                btn.classList.add('border-blue-600', 'text-blue-600');
                btn.classList.remove('border-transparent', 'text-slate-500');
            }
        }

        // Tab retention: restore active tab from URL hash on page load
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#tab-')) {
                const tabName = hash.substring(5); // remove '#tab-'
                showTab(tabName);
                // Scroll the tab nav into view after switching
                const tabNav = document.getElementById('tabNav');
                if (tabNav) tabNav.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });

        function openPulihkanModal(hukumanId, jenisSanksiValue) {
            var modal = document.getElementById('pulihkanModal');
            var form = document.getElementById('pulihkanForm');
            form.action = '/riwayat/hukuman/' + hukumanId + '/pulihkan';

            // Toggle restoration fields based on jenis_sanksi
            // 3 = PenurunanPangkat, 4 = PenurunanJabatan, 5 = PembebasanJabatan
            var pangkatEl = document.getElementById('restorationPangkatSection');
            var jabatanEl = document.getElementById('restorationJabatanSection');
            pangkatEl.style.display = (jenisSanksiValue === 3) ? 'block' : 'none';
            jabatanEl.style.display = (jenisSanksiValue === 4 || jenisSanksiValue === 5) ? 'block' : 'none';

            modal.style.display = 'block';
        }

        function closePulihkanModal() {
            document.getElementById('pulihkanModal').style.display = 'none';
        }
    </script>
@endpush

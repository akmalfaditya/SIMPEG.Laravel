@php $p = $pegawai ?? null; $isEdit = isset($pegawai); @endphp
@php $inputClass = 'w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800'; @endphp

{{-- Section: Data Pribadi --}}
<h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-200">Data Pribadi</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">NIP <span class="text-rose-500">*</span></label>
        <input type="text" name="nip" value="{{ old('nip', $p?->nip) }}" required maxlength="18" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Gelar Depan</label>
        <input type="text" name="gelar_depan" value="{{ old('gelar_depan', $p?->gelar_depan) }}" placeholder="Dr., Drs., Ir., Prof." class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap (tanpa gelar) <span class="text-rose-500">*</span></label>
        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $p?->nama_lengkap) }}" required class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Gelar Belakang</label>
        <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang', $p?->gelar_belakang) }}" placeholder="S.H., M.H., S.E., M.Sc." class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir</label>
        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $p?->tempat_lahir) }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $p?->tanggal_lahir?->format('Y-m-d')) }}" required class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
        <select name="jenis_kelamin_id" required class="{{ $inputClass }}">
            <option value="">-- Pilih --</option>
            @foreach($jenisKelaminOptions as $opt)<option value="{{ $opt->id }}" {{ old('jenis_kelamin_id', $p?->jenis_kelamin_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Agama <span class="text-rose-500">*</span></label>
        <select name="agama_id" required class="{{ $inputClass }}">
            <option value="">-- Pilih --</option>
            @foreach($agamaOptions as $opt)<option value="{{ $opt->id }}" {{ old('agama_id', $p?->agama_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status Pernikahan <span class="text-rose-500">*</span></label>
        <select name="status_pernikahan_id" required class="{{ $inputClass }}">
            <option value="">-- Pilih --</option>
            @foreach($statusPernikahanOptions as $opt)<option value="{{ $opt->id }}" {{ old('status_pernikahan_id', $p?->status_pernikahan_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Golongan Darah <span class="text-rose-500">*</span></label>
        <select name="golongan_darah_id" required class="{{ $inputClass }}">
            <option value="">-- Pilih --</option>
            @foreach($golonganDarahOptions as $opt)<option value="{{ $opt->id }}" {{ old('golongan_darah_id', $p?->golongan_darah_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $p?->email) }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No Telepon</label>
        <input type="text" name="no_telepon" value="{{ old('no_telepon', $p?->no_telepon) }}" class="{{ $inputClass }}">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
        <textarea name="alamat" rows="2" class="{{ $inputClass }}">{{ old('alamat', $p?->alamat) }}</textarea>
    </div>
</div>

{{-- Section: Data Kepegawaian --}}
<h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-200">Data Kepegawaian</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Pegawai <span class="text-rose-500">*</span></label>
        <select name="tipe_pegawai_id" required class="{{ $inputClass }}">
            <option value="">-- Pilih --</option>
            @foreach($tipePegawaiOptions as $opt)
                <option value="{{ $opt->id }}" {{ old('tipe_pegawai_id', $p?->tipe_pegawai_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
        <select name="status_kepegawaian_id" required class="{{ $inputClass }}">
            <option value="">-- Pilih --</option>
            @foreach($statusKepegawaianOptions as $opt)
                <option value="{{ $opt->id }}" {{ old('status_kepegawaian_id', $p?->status_kepegawaian_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">TMT CPNS <span class="text-rose-500">*</span><x-tooltip text="Terhitung Mulai Tanggal pengangkatan CPNS" /></label>
        <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $p?->tmt_cpns?->format('Y-m-d')) }}" required class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">TMT PNS<x-tooltip text="Terhitung Mulai Tanggal pengangkatan PNS" /></label>
        <input type="date" name="tmt_pns" value="{{ old('tmt_pns', $p?->tmt_pns?->format('Y-m-d')) }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Bagian</label>
        <select name="bagian_id" class="{{ $inputClass }} searchable-select">
            <option value="">-- Pilih Bagian --</option>
            @foreach($bagianOptions as $opt)
                <option value="{{ $opt->id }}" {{ old('bagian_id', $p?->bagian_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Unit Kerja</label>
        <select name="unit_kerja_id" class="{{ $inputClass }} searchable-select">
            <option value="">-- Pilih Unit Kerja --</option>
            @foreach($unitKerjaOptions as $opt)
                <option value="{{ $opt->id }}" {{ old('unit_kerja_id', $p?->unit_kerja_id) == $opt->id ? 'selected' : '' }}>{{ $opt->nama }}</option>
            @endforeach
        </select>
    </div>
    @if($isEdit)
    <div class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-xs font-medium text-blue-700">Golongan, jabatan, dan gaji pokok dikelola otomatis melalui Riwayat Pangkat, Riwayat Jabatan &amp; Proses KGB.</p>
                <p class="text-xs text-blue-600 mt-0.5">Untuk mengubah data tersebut, gunakan tab riwayat di halaman Detail Pegawai.</p>
            </div>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Gaji Pokok</label>
        <input type="text" value="Rp {{ number_format($p->gaji_pokok, 0, ',', '.') }}" readonly disabled class="{{ $inputClass }} bg-slate-100 text-slate-500 cursor-not-allowed">
        <input type="hidden" name="gaji_pokok" value="{{ $p->gaji_pokok }}">
    </div>
    @endif
</div>

{{-- Section: Dokumen Dasar --}}
<h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-200">Dokumen Dasar</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Upload SK CPNS (PDF)</label>
        <input type="file" name="sk_cpns_file" accept=".pdf"
            class="{{ $inputClass }} file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
        <p class="text-xs text-slate-400 mt-1">Maks. 5MB, format PDF</p>
        @if($isEdit && $p?->sk_cpns_path)
            <div class="mt-2">
                <a href="{{ route('dokumen.download', ['type' => 'sk_cpns', 'id' => $p->id]) }}" target="_blank"
                    class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Lihat SK CPNS saat ini
                </a>
            </div>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Upload SK PNS (PDF)</label>
        <input type="file" name="sk_pns_file" accept=".pdf"
            class="{{ $inputClass }} file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
        <p class="text-xs text-slate-400 mt-1">Maks. 5MB, format PDF</p>
        @if($isEdit && $p?->sk_pns_path)
            <div class="mt-2">
                <a href="{{ route('dokumen.download', ['type' => 'sk_pns', 'id' => $p->id]) }}" target="_blank"
                    class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Lihat SK PNS saat ini
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Section: Pangkat & Jabatan Awal (CREATE ONLY) --}}
@unless($isEdit)
<h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-200">Pangkat & Jabatan Awal</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50 border border-blue-200 rounded-lg mb-6">
    <p class="md:col-span-2 text-xs text-blue-700">Gaji pokok akan dihitung otomatis berdasarkan golongan yang dipilih. Riwayat pangkat dan jabatan awal dibuat otomatis.</p>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Golongan/Pangkat <span class="text-rose-500">*</span></label>
        <select name="golongan_id" required class="{{ $inputClass }} searchable-select">
            <option value="">-- Pilih Golongan --</option>
            @foreach($golonganOptions as $g)
                <option value="{{ $g->id }}" {{ old('golongan_id') == $g->id ? 'selected' : '' }}>{{ $g->label }} — {{ $g->pangkat }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan <span class="text-rose-500">*</span></label>
        <select name="jabatan_id" required class="{{ $inputClass }} searchable-select">
            <option value="">-- Pilih Jabatan --</option>
            @foreach($jabatanOptions as $j)
                <option value="{{ $j->id }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
            @endforeach
        </select>
    </div>
</div>
@endunless

{{-- Section: Identitas Tambahan --}}
<h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-200">Identitas Tambahan</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">NPWP</label>
        <input type="text" name="npwp" value="{{ old('npwp', $p?->npwp) }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No Karpeg</label>
        <input type="text" name="no_karpeg" value="{{ old('no_karpeg', $p?->no_karpeg) }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No Taspen</label>
        <input type="text" name="no_taspen" value="{{ old('no_taspen', $p?->no_taspen) }}" class="{{ $inputClass }}">
    </div>
</div>

@if($errors->any())
<div class="mt-4 p-3 bg-rose-50 border border-rose-200 rounded-lg">
    <ul class="text-xs text-rose-600 list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

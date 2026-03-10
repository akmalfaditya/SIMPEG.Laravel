@php $p = $pegawai ?? null; $isEdit = isset($pegawai); @endphp
@php $inputClass = 'w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400'; @endphp

{{-- Section: Data Pribadi --}}
<h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-200">Data Pribadi</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">NIP <span class="text-red-500">*</span></label>
        <input type="text" name="nip" value="{{ old('nip', $p?->nip) }}" required maxlength="18" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Gelar Depan</label>
        <input type="text" name="gelar_depan" value="{{ old('gelar_depan', $p?->gelar_depan) }}" placeholder="Dr., Drs., Ir., Prof." class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap (tanpa gelar) <span class="text-red-500">*</span></label>
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
        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $p?->tanggal_lahir?->format('Y-m-d')) }}" required class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin" required class="{{ $inputClass }}">
            @foreach($jenisKelaminOptions as $opt)<option value="{{ $opt->value }}" {{ old('jenis_kelamin', $p?->jenis_kelamin?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Agama <span class="text-red-500">*</span></label>
        <select name="agama" required class="{{ $inputClass }}">
            @foreach($agamaOptions as $opt)<option value="{{ $opt->value }}" {{ old('agama', $p?->agama?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status Pernikahan <span class="text-red-500">*</span></label>
        <select name="status_pernikahan" required class="{{ $inputClass }}">
            @foreach($statusPernikahanOptions as $opt)<option value="{{ $opt->value }}" {{ old('status_pernikahan', $p?->status_pernikahan?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Golongan Darah <span class="text-red-500">*</span></label>
        <select name="golongan_darah" required class="{{ $inputClass }}">
            @foreach($golonganDarahOptions as $opt)<option value="{{ $opt->value }}" {{ old('golongan_darah', $p?->golongan_darah?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
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
        <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Pegawai <span class="text-red-500">*</span></label>
        <select name="tipe_pegawai" required class="{{ $inputClass }}">
            @foreach(['PNS', 'CPNS', 'PPPK'] as $tipe)
                <option value="{{ $tipe }}" {{ old('tipe_pegawai', $p?->tipe_pegawai ?? 'PNS') === $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status Kepegawaian <span class="text-red-500">*</span></label>
        <select name="status_kepegawaian" required class="{{ $inputClass }}">
            @foreach(['Aktif', 'Tidak Aktif'] as $status)
                <option value="{{ $status }}" {{ old('status_kepegawaian', $p?->status_kepegawaian ?? 'Aktif') === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">TMT CPNS <span class="text-red-500">*</span></label>
        <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $p?->tmt_cpns?->format('Y-m-d')) }}" required class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">TMT PNS</label>
        <input type="date" name="tmt_pns" value="{{ old('tmt_pns', $p?->tmt_pns?->format('Y-m-d')) }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Bagian</label>
        <select name="bagian" class="{{ $inputClass }}">
            <option value="">-- Pilih Bagian --</option>
            @foreach(['Tata Usaha', 'Tikim', 'Lantaskim', 'Inteldakim', 'Intaltuskim'] as $bagian)
                <option value="{{ $bagian }}" {{ old('bagian', $p?->bagian) === $bagian ? 'selected' : '' }}>{{ $bagian }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Unit Kerja</label>
        <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $p?->unit_kerja ?? 'Kanim Jakut') }}" class="{{ $inputClass }}">
    </div>
    @if($isEdit)
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Gaji Pokok <span class="text-red-500">*</span></label>
        <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok', $p?->gaji_pokok) }}" required class="{{ $inputClass }}">
    </div>
    @endif
</div>

{{-- Section: Pangkat & Jabatan Awal (CREATE ONLY) --}}
@unless($isEdit)
<h3 class="text-sm font-semibold text-slate-800 mb-3 pb-2 border-b border-slate-200">Pangkat & Jabatan Awal</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50 border border-blue-200 rounded-xl mb-6">
    <p class="md:col-span-2 text-xs text-blue-700">Gaji pokok akan dihitung otomatis berdasarkan golongan yang dipilih. Riwayat pangkat dan jabatan awal dibuat otomatis.</p>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Golongan/Pangkat <span class="text-red-500">*</span></label>
        <select name="golongan_id" required class="{{ $inputClass }}">
            <option value="">-- Pilih Golongan --</option>
            @foreach($golonganOptions as $g)
                <option value="{{ $g->id }}" {{ old('golongan_id') == $g->id ? 'selected' : '' }}>{{ $g->label }} — {{ $g->pangkat }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
        <select name="jabatan_id" required class="{{ $inputClass }}">
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
<div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
    <ul class="text-xs text-red-600 list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

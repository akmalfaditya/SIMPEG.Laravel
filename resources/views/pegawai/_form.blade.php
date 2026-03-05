@php $p = $pegawai ?? null; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">NIP <span class="text-red-500">*</span></label>
        <input type="text" name="nip" value="{{ old('nip', $p?->nip) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $p?->nama_lengkap) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir</label>
        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $p?->tempat_lahir) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $p?->tanggal_lahir?->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
            @foreach($jenisKelaminOptions as $opt)<option value="{{ $opt->value }}" {{ old('jenis_kelamin', $p?->jenis_kelamin?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Agama <span class="text-red-500">*</span></label>
        <select name="agama" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
            @foreach($agamaOptions as $opt)<option value="{{ $opt->value }}" {{ old('agama', $p?->agama?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status Pernikahan <span class="text-red-500">*</span></label>
        <select name="status_pernikahan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
            @foreach($statusPernikahanOptions as $opt)<option value="{{ $opt->value }}" {{ old('status_pernikahan', $p?->status_pernikahan?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Golongan Darah <span class="text-red-500">*</span></label>
        <select name="golongan_darah" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
            @foreach($golonganDarahOptions as $opt)<option value="{{ $opt->value }}" {{ old('golongan_darah', $p?->golongan_darah?->value) == $opt->value ? 'selected' : '' }}>{{ $opt->label() }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $p?->email) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No Telepon</label>
        <input type="text" name="no_telepon" value="{{ old('no_telepon', $p?->no_telepon) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
        <textarea name="alamat" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">{{ old('alamat', $p?->alamat) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">TMT CPNS <span class="text-red-500">*</span></label>
        <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $p?->tmt_cpns?->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">TMT PNS</label>
        <input type="date" name="tmt_pns" value="{{ old('tmt_pns', $p?->tmt_pns?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Gaji Pokok <span class="text-red-500">*</span></label>
        <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok', $p?->gaji_pokok) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Unit Kerja</label>
        <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $p?->unit_kerja) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">NPWP</label>
        <input type="text" name="npwp" value="{{ old('npwp', $p?->npwp) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No Karpeg</label>
        <input type="text" name="no_karpeg" value="{{ old('no_karpeg', $p?->no_karpeg) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No Taspen</label>
        <input type="text" name="no_taspen" value="{{ old('no_taspen', $p?->no_taspen) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
    </div>
</div>

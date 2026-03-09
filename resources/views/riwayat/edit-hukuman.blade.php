@extends('layouts.app')
@section('title', 'Edit Hukuman Disiplin')
@section('header', 'Edit Riwayat Hukuman Disiplin')
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.hukuman.update', $riwayat) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Hukuman *</label><select name="tingkat_hukuman" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">@foreach($tingkatOptions as $t)<option value="{{ $t->value }}" {{ old('tingkat_hukuman', $riwayat->tingkat_hukuman->value) == $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jenis Sanksi *</label><select name="jenis_sanksi" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">@foreach($sanksiOptions as $s)<option value="{{ $s->value }}" {{ old('jenis_sanksi', $riwayat->jenis_sanksi->value) == $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Durasi Hukuman (Tahun)</label><input type="number" name="durasi_tahun" value="{{ old('durasi_tahun', $riwayat->durasi_tahun) }}" min="1" max="10" placeholder="contoh: 1" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label><input type="text" name="nomor_sk" value="{{ old('nomor_sk', $riwayat->nomor_sk) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK</label><input type="date" name="tanggal_sk" value="{{ old('tanggal_sk', $riwayat->tanggal_sk?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Hukuman *</label><input type="date" name="tmt_hukuman" value="{{ old('tmt_hukuman', $riwayat->tmt_hukuman->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Selesai</label><input type="date" name="tmt_selesai_hukuman" value="{{ old('tmt_selesai_hukuman', $riwayat->tmt_selesai_hukuman?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="deskripsi" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">{{ old('deskripsi', $riwayat->deskripsi) }}</textarea></div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Upload SK Hukdis (PDF, maks 5MB)</label>
                @if($riwayat->file_pdf_path)
                <p class="text-xs text-emerald-600 mb-1">File saat ini: {{ basename($riwayat->file_pdf_path) }}</p>
                @endif
                <input type="file" name="file_sk" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Link Google Drive SK</label><input type="url" name="google_drive_link" value="{{ old('google_drive_link', $riwayat->google_drive_link) }}" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
        </div>
        @if($errors->any())
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg"><ul class="text-xs text-red-600 list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Perbarui</button>
            <a href="{{ route('pegawai.show', $riwayat->pegawai_id) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

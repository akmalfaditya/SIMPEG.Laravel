@extends('layouts.app')
@section('title', 'Tambah Hukuman Disiplin')
@section('header', 'Tambah Riwayat Hukuman Disiplin')
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.hukuman.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawaiId }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Hukuman *</label><select name="tingkat_hukuman" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">@foreach($tingkatOptions as $t)<option value="{{ $t->value }}" {{ old('tingkat_hukuman') == $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jenis Sanksi *</label><select name="jenis_sanksi" id="jenisSanksi" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">@foreach($sanksiOptions as $s)<option value="{{ $s->value }}" {{ old('jenis_sanksi') == $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Durasi Hukuman (Tahun)</label><input type="number" name="durasi_tahun" value="{{ old('durasi_tahun') }}" min="1" max="10" placeholder="contoh: 1" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label><input type="text" name="nomor_sk" value="{{ old('nomor_sk') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK</label><input type="date" name="tanggal_sk" value="{{ old('tanggal_sk') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Hukuman *</label><input type="date" name="tmt_hukuman" value="{{ old('tmt_hukuman', today()->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Selesai Hukuman</label><input type="date" name="tmt_selesai_hukuman" value="{{ old('tmt_selesai_hukuman') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="deskripsi" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">{{ old('deskripsi') }}</textarea></div>

            {{-- Type 2: Penurunan Pangkat target --}}
            <div id="demotionPangkatSection" class="md:col-span-2 hidden p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-xs font-semibold text-amber-700 mb-2">Penurunan Pangkat — Pilih pangkat baru setelah demosi:</p>
                <select name="demotion_golongan_ruang" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <option value="">-- Pilih Golongan Ruang --</option>
                    @foreach($golonganOptions as $g)<option value="{{ $g->value }}" {{ old('demotion_golongan_ruang') == $g->value ? 'selected' : '' }}>{{ $g->label() }}</option>@endforeach
                </select>
            </div>

            {{-- Type 2: Penurunan/Pembebasan Jabatan target --}}
            <div id="demotionJabatanSection" class="md:col-span-2 hidden p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-xs font-semibold text-amber-700 mb-2">Penurunan/Pembebasan Jabatan — Pilih jabatan baru:</p>
                <select name="demotion_jabatan_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatanOptions as $j)<option value="{{ $j->id }}" {{ old('demotion_jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>@endforeach
                </select>
            </div>

            <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload SK Hukdis (PDF, maks 5MB)</label><input type="file" name="file_sk" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Link Google Drive SK</label><input type="url" name="google_drive_link" value="{{ old('google_drive_link') }}" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
        </div>
        @if($errors->any())
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg"><ul class="text-xs text-red-600 list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Simpan</button>
            <a href="{{ route('pegawai.show', $pegawaiId) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSanksi = document.getElementById('jenisSanksi');
    const pangkatSection = document.getElementById('demotionPangkatSection');
    const jabatanSection = document.getElementById('demotionJabatanSection');

    function toggleDemotionFields() {
        const val = parseInt(jenisSanksi.value);
        // 3 = PenurunanPangkat
        pangkatSection.classList.toggle('hidden', val !== 3);
        // 4 = PenurunanJabatan, 5 = PembebasanJabatan
        jabatanSection.classList.toggle('hidden', val !== 4 && val !== 5);
    }

    jenisSanksi.addEventListener('change', toggleDemotionFields);
    toggleDemotionFields();
});
</script>
@endpush

@extends('layouts.app')
@section('title', 'Tambah Hukuman Disiplin')
@section('header', 'Tambah Riwayat Hukuman Disiplin')
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.hukuman.store') }}">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawaiId }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Hukuman *</label><select name="tingkat_hukuman" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">@foreach($tingkatOptions as $t)<option value="{{ $t->value }}">{{ $t->label() }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jenis Hukuman *</label><input type="text" name="jenis_hukuman" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label><input type="text" name="nomor_sk" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK</label><input type="date" name="tanggal_sk" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Hukuman *</label><input type="date" name="tmt_hukuman" value="{{ today()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Selesai Hukuman</label><input type="date" name="tmt_selesai_hukuman" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="deskripsi" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></textarea></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Simpan</button>
            <a href="{{ route('pegawai.show', $pegawaiId) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

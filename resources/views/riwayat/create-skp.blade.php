@extends('layouts.app')
@section('title', 'Tambah SKP')
@section('header', 'Tambah Penilaian Kinerja (SKP)')
@section('content')
<div class="max-w-md bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.skp.store') }}">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawaiId }}">
        <div class="space-y-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun *</label><input type="number" name="tahun" value="{{ now()->year }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nilai SKP *</label><select name="nilai_skp" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"><option value="Sangat Baik">Sangat Baik</option><option value="Baik" selected>Baik</option><option value="Cukup">Cukup</option><option value="Kurang">Kurang</option><option value="Buruk">Buruk</option></select></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Simpan</button>
            <a href="{{ route('pegawai.show', $pegawaiId) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

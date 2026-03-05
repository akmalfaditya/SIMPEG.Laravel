@extends('layouts.app')
@section('title', 'Tambah Pendidikan')
@section('header', 'Tambah Riwayat Pendidikan')
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.pendidikan.store') }}">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawaiId }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Pendidikan *</label><select name="tingkat_pendidikan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"><option value="SMA">SMA</option><option value="D3">D3</option><option value="S1" selected>S1</option><option value="S2">S2</option><option value="S3">S3</option></select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Institusi *</label><input type="text" name="institusi" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jurusan *</label><input type="text" name="jurusan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun Lulus *</label><input type="number" name="tahun_lulus" value="{{ now()->year }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">No Ijazah</label><input type="text" name="no_ijazah" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Ijazah</label><input type="date" name="tanggal_ijazah" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Simpan</button>
            <a href="{{ route('pegawai.show', $pegawaiId) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

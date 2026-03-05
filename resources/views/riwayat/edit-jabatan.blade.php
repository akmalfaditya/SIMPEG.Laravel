@extends('layouts.app')
@section('title', 'Edit Riwayat Jabatan')
@section('header', 'Edit Riwayat Jabatan')
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.jabatan.update', $riwayat) }}">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jabatan *</label><select name="jabatan_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">@foreach($jabatanOptions as $j)<option value="{{ $j->id }}" {{ $riwayat->jabatan_id == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label><input type="text" name="nomor_sk" value="{{ $riwayat->nomor_sk }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Jabatan *</label><input type="date" name="tmt_jabatan" value="{{ $riwayat->tmt_jabatan->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK *</label><input type="date" name="tanggal_sk" value="{{ $riwayat->tanggal_sk->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label><input type="text" name="google_drive_link" value="{{ $riwayat->google_drive_link }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Perbarui</button>
            <a href="{{ route('pegawai.show', $riwayat->pegawai_id) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

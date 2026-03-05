@extends('layouts.app')
@section('title', 'Edit SKP')
@section('header', 'Edit Penilaian Kinerja (SKP)')
@section('content')
<div class="max-w-md bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.skp.update', $riwayat) }}">
        @csrf @method('PUT')
        <div class="space-y-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun *</label><input type="number" name="tahun" value="{{ $riwayat->tahun }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nilai SKP *</label><select name="nilai_skp" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">@foreach(['Sangat Baik','Baik','Cukup','Kurang','Buruk'] as $v)<option value="{{ $v }}" {{ $riwayat->nilai_skp == $v ? 'selected' : '' }}>{{ $v }}</option>@endforeach</select></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Perbarui</button>
            <a href="{{ route('pegawai.show', $riwayat->pegawai_id) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

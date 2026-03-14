@extends('layouts.app')
@section('title', 'Tambah Riwayat Jabatan')
@section('header', 'Tambah Riwayat Jabatan')
@section('content')
<x-card class="max-w-2xl">
    <form method="POST" action="{{ route('riwayat.jabatan.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawaiId }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jabatan *</label><select name="jabatan_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm searchable-select">@foreach($jabatanOptions as $j)<option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label><input type="text" name="nomor_sk" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Jabatan *<x-tooltip text="Terhitung Mulai Tanggal yang tertera di SK jabatan" /></label><input type="date" name="tmt_jabatan" value="{{ today()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK *</label><input type="date" name="tanggal_sk" value="{{ today()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload SK (PDF, maks 5MB)</label><input type="file" name="file_sk" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label><input type="url" name="google_drive_link" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <x-button type="submit" size="lg">Simpan</x-button>
            <x-button variant="secondary" size="lg" href="{{ route('pegawai.show', $pegawaiId) }}">Batal</x-button>
        </div>
    </form>
</x-card>
@endsection

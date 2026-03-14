@extends('layouts.app')
@section('title', 'Tambah Latihan')
@section('header', 'Tambah Riwayat Latihan Jabatan')
@section('content')
<x-card class="max-w-2xl">
    <form method="POST" action="{{ route('riwayat.latihan.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pegawai_id" value="{{ $pegawaiId }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Latihan *</label><input type="text" name="nama_latihan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun *</label><input type="number" name="tahun_pelaksanaan" value="{{ now()->year }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Jam *</label><input type="number" name="jumlah_jam" value="0" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Penyelenggara</label><input type="text" name="penyelenggara" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tempat</label><input type="text" name="tempat_pelaksanaan" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">No Sertifikat</label><input type="text" name="no_sertifikat" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload Sertifikat (PDF, maks 5MB)</label><input type="file" name="file_sk" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label><input type="url" name="google_drive_link" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <x-button type="submit" size="lg">Simpan</x-button>
            <x-button variant="secondary" size="lg" href="{{ route('pegawai.show', $pegawaiId) }}">Batal</x-button>
        </div>
    </form>
</x-card>
@endsection

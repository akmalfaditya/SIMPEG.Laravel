@extends('layouts.app')
@section('title', 'Edit Latihan')
@section('header', 'Edit Riwayat Latihan Jabatan')
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('riwayat.latihan.update', $riwayat) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Latihan *</label><input type="text" name="nama_latihan" value="{{ $riwayat->nama_latihan }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun *</label><input type="number" name="tahun_pelaksanaan" value="{{ $riwayat->tahun_pelaksanaan }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Jam *</label><input type="number" name="jumlah_jam" value="{{ $riwayat->jumlah_jam }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Penyelenggara</label><input type="text" name="penyelenggara" value="{{ $riwayat->penyelenggara }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tempat</label><input type="text" name="tempat_pelaksanaan" value="{{ $riwayat->tempat_pelaksanaan }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">No Sertifikat</label><input type="text" name="no_sertifikat" value="{{ $riwayat->no_sertifikat }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload Sertifikat (PDF, maks 5MB)</label><input type="file" name="file_sk" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">@if($riwayat->file_pdf_path)<p class="text-xs text-green-600 mt-1">File sudah ada. Upload baru akan mengganti file lama.</p>@endif</div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label><input type="url" name="google_drive_link" value="{{ $riwayat->google_drive_link }}" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl">Perbarui</button>
            <a href="{{ route('pegawai.show', $riwayat->pegawai_id) }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl">Batal</a>
        </div>
    </form>
</div>
@endsection

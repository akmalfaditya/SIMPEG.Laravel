@extends('layouts.app')
@section('title', 'Proses Kenaikan Pangkat')
@section('header', 'Proses Kenaikan Pangkat')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a href="{{ route('kenaikan-pangkat.eligible') }}" class="hover:text-blue-600">Kenaikan Pangkat Eligible</a> / <span class="text-slate-700">Proses</span>
@endsection
@section('content')
<div class="max-w-2xl">
    {{-- Pegawai Info Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Data Pegawai</h3>
        <div class="grid grid-cols-2 gap-y-2 text-sm">
            <span class="text-slate-500">NIP</span><span class="font-mono font-medium text-slate-700">{{ $data['nip'] }}</span>
            <span class="text-slate-500">Nama</span><span class="font-medium text-slate-700">{{ $data['nama_lengkap'] }}</span>
            <span class="text-slate-500">Golongan Saat Ini</span><span class="text-slate-700">{{ $data['golongan_saat_ini'] }}</span>
            <span class="text-slate-500">TMT Pangkat Terakhir</span><span class="text-slate-700">{{ $data['tmt_pangkat_terakhir']->format('d/m/Y') }}</span>
            <span class="text-slate-500">Masa Kerja Total</span><span class="text-slate-700">{{ $data['masa_kerja_total_tahun'] }} tahun</span>
            <span class="text-slate-500">Proyeksi Periode</span><span class="font-medium text-blue-600">{{ $data['proyeksi_periode'] }}</span>
        </div>
    </div>

    {{-- Pangkat & Gaji transition info --}}
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
        <strong>Kenaikan Pangkat:</strong>
        {{ $data['golongan_saat_ini'] }} → <strong>{{ $data['golongan_berikutnya'] }}</strong>.
        Gaji lama <strong>Rp {{ number_format($data['gaji_lama'], 0, ',', '.') }}</strong>
        → Gaji baru <strong>Rp {{ $data['gaji_baru'] ? number_format($data['gaji_baru'], 0, ',', '.') : '-' }}</strong>.
    </div>
    @if(!$data['gaji_baru'])
    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
        <strong>Perhatian:</strong> Gaji baru tidak dapat dihitung otomatis dari tabel gaji. Silakan isi manual.
    </div>
    @endif

    {{-- Process Form --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Form Proses Kenaikan Pangkat</h3>
        <form method="POST" action="{{ route('kenaikan-pangkat.process') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $data['pegawai_id'] }}">
            <input type="hidden" name="golongan_id" value="{{ $data['golongan_berikutnya_id'] }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label>
                    <input type="text" name="nomor_sk" value="{{ old('nomor_sk') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800" placeholder="Nomor SK Kenaikan Pangkat">
                    @error('nomor_sk') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">TMT Pangkat *<x-tooltip text="Terhitung Mulai Tanggal kenaikan pangkat" /></label>
                    <input type="date" name="tmt_pangkat" value="{{ old('tmt_pangkat', $data['proyeksi_tmt']) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('tmt_pangkat') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK</label>
                    <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('tanggal_sk') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gaji Baru *</label>
                    <input type="number" name="gaji_baru" value="{{ old('gaji_baru', $data['gaji_baru']) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('gaji_baru') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Upload SK (PDF, maks 5MB)</label>
                    <input type="file" name="file_sk" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
                    @error('file_sk') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label>
                    <input type="url" name="google_drive_link" value="{{ old('google_drive_link') }}" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('google_drive_link') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            @if ($errors->any())
            <div class="mt-4 p-3 bg-rose-50 border border-rose-200 rounded-lg text-sm text-rose-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <x-button type="submit" size="lg">Proses Kenaikan Pangkat</x-button>
                <x-button variant="secondary" size="lg" href="{{ route('kenaikan-pangkat.eligible') }}">Batal</x-button>
            </div>
        </form>
    </div>
</div>
@endsection

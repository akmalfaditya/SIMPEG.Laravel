@extends('layouts.app')
@section('title', 'Proses KGB')
@section('header', 'Proses KGB')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a href="{{ route('kgb.eligible') }}" class="hover:text-blue-600">KGB Eligible</a> / <span class="text-slate-700">Proses KGB</span>
@endsection
@section('content')
<div class="max-w-2xl">
    {{-- Pegawai Info Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Data Pegawai</h3>
        <div class="grid grid-cols-2 gap-y-2 text-sm">
            <span class="text-slate-500">NIP</span><span class="font-mono font-medium text-slate-700">{{ $data['nip'] }}</span>
            <span class="text-slate-500">Nama</span><span class="font-medium text-slate-700">{{ $data['nama_lengkap'] }}</span>
            <span class="text-slate-500">Pangkat</span><span class="text-slate-700">{{ $data['pangkat_terakhir'] }}</span>
            <span class="text-slate-500">TMT KGB Terakhir</span><span class="text-slate-700">{{ $data['tmt_kgb_terakhir']->format('d/m/Y') }}</span>
            <span class="text-slate-500">Jatuh Tempo KGB</span><span class="font-medium text-rose-600">{{ $data['tanggal_jatuh_tempo']->format('d/m/Y') }}</span>
        </div>
    </div>

    {{-- Pre-calculated salary info --}}
    @if($data['gaji_baru'])
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
        <strong>Info:</strong> Gaji baru dihitung otomatis berdasarkan PP 15/2019.
        Gaji lama <strong>Rp {{ number_format($data['gaji_lama'], 0, ',', '.') }}</strong>
        → Gaji baru <strong>Rp {{ number_format($data['gaji_baru'], 0, ',', '.') }}</strong>
        (MKG {{ $data['masa_kerja_tahun'] }} tahun).
    </div>
    @else
    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
        <strong>Perhatian:</strong> Gaji baru tidak dapat dihitung otomatis dari tabel gaji. Silakan isi manual.
    </div>
    @endif

    {{-- Process Form --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Form Proses KGB</h3>
        <form method="POST" action="{{ route('kgb.process') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $data['pegawai_id'] }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label>
                    <input type="text" name="nomor_sk" value="{{ old('nomor_sk') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800" placeholder="Nomor SK KGB">
                    @error('nomor_sk') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">TMT KGB Baru *<x-tooltip text="Terhitung Mulai Tanggal kenaikan gaji berkala" /></label>
                    <input type="date" name="tmt_kgb" value="{{ old('tmt_kgb', $data['tanggal_jatuh_tempo']->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('tmt_kgb') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gaji Lama *</label>
                    <input type="number" name="gaji_lama" value="{{ old('gaji_lama', $data['gaji_lama']) }}" required readonly class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-600">
                    @error('gaji_lama') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gaji Baru *</label>
                    <input type="number" name="gaji_baru" value="{{ old('gaji_baru', $data['gaji_baru']) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('gaji_baru') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Masa Kerja Gol. (Tahun) *</label>
                    <input type="number" name="masa_kerja_golongan_tahun" value="{{ old('masa_kerja_golongan_tahun', $data['masa_kerja_tahun']) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('masa_kerja_golongan_tahun') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Masa Kerja Gol. (Bulan) *</label>
                    <input type="number" name="masa_kerja_golongan_bulan" value="{{ old('masa_kerja_golongan_bulan', 0) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-blue-800">
                    @error('masa_kerja_golongan_bulan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
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
                <x-button type="submit" size="lg">Proses KGB</x-button>
                <x-button variant="secondary" size="lg" href="{{ route('kgb.eligible') }}">Batal</x-button>
            </div>
        </form>
    </div>
</div>
@endsection

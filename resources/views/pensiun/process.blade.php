@extends('layouts.app')
@section('title', 'Proses Pensiun')
@section('header', 'Proses Pensiun')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a href="{{ route('pensiun.index') }}" class="hover:text-blue-600">Pensiun</a> / <span class="text-slate-700">Proses Pensiun</span>
@endsection
@section('content')
<div class="max-w-2xl">
    {{-- Pegawai Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Data Pegawai</h3>
        <div class="grid grid-cols-2 gap-y-2 text-sm">
            <span class="text-slate-500">NIP</span><span class="font-mono font-medium text-slate-700">{{ $data['nip'] }}</span>
            <span class="text-slate-500">Nama</span><span class="font-medium text-slate-700">{{ $data['nama_lengkap'] }}</span>
            <span class="text-slate-500">Pangkat Terakhir</span><span class="text-slate-700">{{ $data['pangkat_terakhir'] }}</span>
            <span class="text-slate-500">Jabatan Terakhir</span><span class="text-slate-700">{{ $data['jabatan_terakhir'] }}</span>
            <span class="text-slate-500">BUP</span><span class="text-slate-700">{{ $data['bup'] }} tahun</span>
            <span class="text-slate-500">Masa Kerja</span><span class="text-slate-700">{{ $data['masa_kerja'] }}</span>
            <span class="text-slate-500">Gaji Pokok Terakhir</span><span class="font-medium text-slate-700">Rp {{ number_format($data['gaji_pokok'], 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Alert info --}}
    @php
        $alertColors = [
            'Hitam' => 'bg-slate-800 border-slate-700 text-white',
            'Merah' => 'bg-red-50 border-red-200 text-red-700',
            'Kuning' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
            'Hijau' => 'bg-green-50 border-green-200 text-green-700',
        ];
        $alertMsg = [
            'Hitam' => 'Pegawai sudah melewati Batas Usia Pensiun.',
            'Merah' => 'Pegawai akan mencapai BUP dalam ' . $data['bulan_menuju_pensiun'] . ' bulan.',
            'Kuning' => 'Pegawai akan mencapai BUP dalam ' . $data['bulan_menuju_pensiun'] . ' bulan.',
            'Hijau' => 'Pegawai akan mencapai BUP dalam ' . $data['bulan_menuju_pensiun'] . ' bulan.',
        ];
    @endphp
    <div class="mb-4 p-3 border rounded-lg text-sm {{ $alertColors[$data['alert_level']] ?? '' }}">
        <strong>{{ $data['alert_level'] }}:</strong> {{ $alertMsg[$data['alert_level']] ?? '' }}
        Tanggal pensiun: <strong>{{ $data['tanggal_pensiun']->format('d/m/Y') }}</strong>.
    </div>

    {{-- Process Form --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Form Proses Pensiun</h3>
        <form method="POST" action="{{ route('pensiun.process') }}">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $data['pegawai_id'] }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK Pensiun *</label>
                    <input type="text" name="sk_pensiun_nomor" value="{{ old('sk_pensiun_nomor') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nomor SK Pensiun">
                    @error('sk_pensiun_nomor') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK Pensiun *</label>
                    <input type="date" name="sk_pensiun_tanggal" value="{{ old('sk_pensiun_tanggal', now()->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('sk_pensiun_tanggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">TMT Pensiun *</label>
                    <input type="date" name="tmt_pensiun" value="{{ old('tmt_pensiun', $data['tanggal_pensiun']->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-slate-400 mt-1">Tanggal pensiun berdasarkan BUP: {{ $data['tanggal_pensiun']->format('d/m/Y') }}</p>
                    @error('tmt_pensiun') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                    <textarea name="catatan_pensiun" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan tambahan (opsional)">{{ old('catatan_pensiun') }}</textarea>
                    @error('catatan_pensiun') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            @if ($errors->any())
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
                <strong>Perhatian:</strong> Memproses pensiun akan mengubah status kepegawaian menjadi <strong>Pensiun</strong> dan menonaktifkan pegawai (<code>is_active = false</code>). Tindakan ini tidak dapat dibatalkan dari halaman ini.
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                    Proses Pensiun
                </button>
                <a href="{{ route('pensiun.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm rounded-xl transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

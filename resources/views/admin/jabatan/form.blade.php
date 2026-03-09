@extends('layouts.app')
@section('title', $jabatan ? 'Edit Jabatan' : 'Tambah Jabatan')
@section('header', $jabatan ? 'Edit Jabatan' : 'Tambah Jabatan Baru')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a href="{{ route('admin.jabatan.index') }}" class="hover:text-blue-600">Master Jabatan</a> / <span class="text-slate-700">{{ $jabatan ? 'Edit' : 'Tambah' }}</span>
@endsection
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ $jabatan ? route('admin.jabatan.update', $jabatan) : route('admin.jabatan.store') }}">
        @csrf
        @if($jabatan) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Nama Jabatan --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_jabatan" value="{{ old('nama_jabatan', $jabatan?->nama_jabatan) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400"
                    placeholder="Contoh: Kepala Sub Bagian Kepegawaian">
                @error('nama_jabatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jenis Jabatan --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Jabatan <span class="text-red-500">*</span></label>
                <select name="jenis_jabatan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($jenisJabatanList as $jenis)
                        <option value="{{ $jenis->value }}" {{ (int) old('jenis_jabatan', $jabatan?->jenis_jabatan?->value) === $jenis->value ? 'selected' : '' }}>{{ $jenis->label() }}</option>
                    @endforeach
                </select>
                @error('jenis_jabatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Rumpun --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rumpun <span class="text-red-500">*</span></label>
                <select name="rumpun" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                    <option value="">-- Pilih Rumpun --</option>
                    @foreach($rumpunList as $r)
                        <option value="{{ $r->value }}" {{ (int) old('rumpun', $jabatan?->rumpun?->value) === $r->value ? 'selected' : '' }}>{{ $r->label() }}</option>
                    @endforeach
                </select>
                @error('rumpun') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- BUP --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">BUP (Batas Usia Pensiun) <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input type="number" name="bup" value="{{ old('bup', $jabatan?->bup ?? 58) }}" required min="50" max="70"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                    <span class="text-sm text-slate-500 whitespace-nowrap">tahun</span>
                </div>
                @error('bup') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Eselon Level --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Level Eselon</label>
                <select name="eselon_level" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                    <option value="0" {{ (int) old('eselon_level', $jabatan?->eselon_level ?? 0) === 0 ? 'selected' : '' }}>Non-Eselon</option>
                    <option value="1" {{ (int) old('eselon_level', $jabatan?->eselon_level) === 1 ? 'selected' : '' }}>Eselon I</option>
                    <option value="2" {{ (int) old('eselon_level', $jabatan?->eselon_level) === 2 ? 'selected' : '' }}>Eselon II</option>
                    <option value="3" {{ (int) old('eselon_level', $jabatan?->eselon_level) === 3 ? 'selected' : '' }}>Eselon III</option>
                    <option value="4" {{ (int) old('eselon_level', $jabatan?->eselon_level) === 4 ? 'selected' : '' }}>Eselon IV</option>
                    <option value="5" {{ (int) old('eselon_level', $jabatan?->eselon_level) === 5 ? 'selected' : '' }}>Eselon V</option>
                </select>
                @error('eselon_level') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Kelas Jabatan --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kelas Jabatan</label>
                <input type="number" name="kelas_jabatan" value="{{ old('kelas_jabatan', $jabatan?->kelas_jabatan ?? 1) }}" min="1" max="17"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                @error('kelas_jabatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md transition-all">
                {{ $jabatan ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.jabatan.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection

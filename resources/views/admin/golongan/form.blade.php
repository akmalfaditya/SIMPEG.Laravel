@extends('layouts.app')
@section('title', $golonganPangkat ? 'Edit Golongan & Pangkat' : 'Tambah Golongan & Pangkat')
@section('header', $golonganPangkat ? 'Edit Golongan & Pangkat' : 'Tambah Golongan & Pangkat')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a href="{{ route('admin.golongan.index') }}" class="hover:text-blue-600">Master Golongan</a> / <span class="text-slate-700">{{ $golonganPangkat ? 'Edit' : 'Tambah' }}</span>
@endsection
@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ $golonganPangkat ? route('admin.golongan.update', $golonganPangkat) : route('admin.golongan.store') }}">
        @csrf
        @if($golonganPangkat) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Golongan/Ruang --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Golongan/Ruang <span class="text-red-500">*</span></label>
                @if($golonganPangkat)
                    <input type="text" value="{{ $golonganPangkat->label }}" disabled
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-500">
                @else
                    <select name="golongan_ruang" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                        <option value="">-- Pilih Golongan --</option>
                        @foreach($availableGolongan as $g)
                            <option value="{{ $g->value }}" {{ (int) old('golongan_ruang') === $g->value ? 'selected' : '' }}>{{ $g->label() }}</option>
                        @endforeach
                    </select>
                    @error('golongan_ruang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                @endif
            </div>

            {{-- Pangkat --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Pangkat <span class="text-red-500">*</span></label>
                <input type="text" name="pangkat" value="{{ old('pangkat', $golonganPangkat?->pangkat) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400"
                    placeholder="Contoh: Penata Muda">
                @error('pangkat') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Min Pendidikan --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pendidikan Minimum</label>
                <select name="min_pendidikan" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                    <option value="">-- Opsional --</option>
                    @foreach(['SD/Sederajat', 'SMP/Sederajat', 'SMA/SMK/Sederajat', 'D-I', 'D-II', 'D-III', 'D-IV/S-1', 'S-2', 'S-3'] as $pend)
                        <option value="{{ $pend }}" {{ old('min_pendidikan', $golonganPangkat?->min_pendidikan) === $pend ? 'selected' : '' }}>{{ $pend }}</option>
                    @endforeach
                </select>
                @error('min_pendidikan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Keterangan --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400"
                    placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $golonganPangkat?->keterangan) }}</textarea>
                @error('keterangan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md transition-all">
                {{ $golonganPangkat ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.golongan.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection

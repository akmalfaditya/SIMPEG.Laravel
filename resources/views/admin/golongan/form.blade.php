@extends('layouts.app')
@section('title', $golonganPangkat ? 'Edit Golongan & Pangkat' : 'Tambah Golongan & Pangkat')
@section('header', $golonganPangkat ? 'Edit Golongan & Pangkat' : 'Tambah Golongan & Pangkat')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a
        href="{{ route('admin.golongan.index') }}" class="hover:text-blue-600">Master Golongan</a> / <span
        class="text-slate-700">{{ $golonganPangkat ? 'Edit' : 'Tambah' }}</span>
@endsection
@section('content')
    <x-card class="max-w-2xl">
        <form method="POST"
            action="{{ $golonganPangkat ? route('admin.golongan.update', $golonganPangkat) : route('admin.golongan.store') }}">
            @csrf
            @if ($golonganPangkat)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Golongan/Ruang --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Golongan/Ruang <span
                            class="text-rose-500">*</span></label>
                    @if ($golonganPangkat)
                        <input type="text" value="{{ $golonganPangkat->label }}" disabled
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-500">
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <input type="number" name="golongan_ruang" value="{{ old('golongan_ruang') }}" required
                                    min="1" max="17" placeholder="Nomor (1-17)"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800">
                                @error('golongan_ruang')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <input type="text" name="label" value="{{ old('label') }}" required
                                    placeholder="Label (cth: I/a)"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800">
                                @error('label')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Pangkat --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Golongan Group <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="golongan_group"
                        value="{{ old('golongan_group', $golonganPangkat?->golongan_group) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800"
                        placeholder="Contoh: I, II, III, IV">
                    @error('golongan_group')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Pangkat --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Pangkat <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="pangkat" value="{{ old('pangkat', $golonganPangkat?->pangkat) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800"
                        placeholder="Contoh: Penata Muda">
                    @error('pangkat')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Min Pendidikan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pendidikan Minimum</label>
                    <select name="min_pendidikan"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800">
                        <option value="">-- Opsional --</option>
                        @foreach (['SD/Sederajat', 'SMP/Sederajat', 'SMA/SMK/Sederajat', 'D-I', 'D-II', 'D-III', 'D-IV/S-1', 'S-2', 'S-3'] as $pend)
                            <option value="{{ $pend }}"
                                {{ old('min_pendidikan', $golonganPangkat?->min_pendidikan) === $pend ? 'selected' : '' }}>
                                {{ $pend }}</option>
                        @endforeach
                    </select>
                    @error('min_pendidikan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800"
                        placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $golonganPangkat?->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <x-button type="submit" size="lg">
                    {{ $golonganPangkat ? 'Perbarui' : 'Simpan' }}
                </x-button>
                <x-button variant="secondary" size="lg" href="{{ route('admin.golongan.index') }}">Batal</x-button>
            </div>
        </form>
    </x-card>
@endsection

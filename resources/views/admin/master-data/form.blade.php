@extends('layouts.app')
@section('title', ($item ? 'Edit' : 'Tambah') . ' ' . $label)
@section('header', ($item ? 'Edit' : 'Tambah') . ' ' . $label)
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> /
    <a href="{{ route('admin.master-data.index', $entity) }}" class="hover:text-blue-600">Master {{ $label }}</a> /
    <span class="text-slate-700">{{ $item ? 'Edit' : 'Tambah' }}</span>
@endsection
@section('content')
    <x-card class="max-w-lg">
        <form method="POST"
            action="{{ $item ? route('admin.master-data.update', [$entity, $item->id]) : route('admin.master-data.store', $entity) }}">
            @csrf
            @if ($item) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama {{ $label }} <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $item?->nama) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800"
                    placeholder="Masukkan nama {{ strtolower($label) }}">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if ($entity === 'tingkat-pendidikan')
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Bobot DUK <span class="text-rose-500">*</span></label>
                <input type="number" name="bobot" value="{{ old('bobot', $item?->bobot ?? 1) }}" required min="1" max="99"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800"
                    placeholder="Masukkan nilai bobot numerik">
                <p class="text-xs text-slate-500 mt-1">Bobot menentukan urutan DUK (semakin besar semakin prioritas, contoh: S3 = 6, SMA = 2).</p>
                @error('bobot')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <x-button type="submit" size="lg">
                    {{ $item ? 'Perbarui' : 'Simpan' }}
                </x-button>
                <x-button variant="secondary" size="lg" href="{{ route('admin.master-data.index', $entity) }}">Batal</x-button>
            </div>
        </form>
    </x-card>
@endsection

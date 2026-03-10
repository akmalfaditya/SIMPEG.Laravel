@extends('layouts.app')
@section('title', ($item ? 'Edit' : 'Tambah') . ' ' . $label)
@section('header', ($item ? 'Edit' : 'Tambah') . ' ' . $label)
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> /
    <a href="{{ route('admin.master-data.index', $entity) }}" class="hover:text-blue-600">Master {{ $label }}</a> /
    <span class="text-slate-700">{{ $item ? 'Edit' : 'Tambah' }}</span>
@endsection
@section('content')
    <div class="max-w-lg bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST"
            action="{{ $item ? route('admin.master-data.update', [$entity, $item->id]) : route('admin.master-data.store', $entity) }}">
            @csrf
            @if ($item) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama {{ $label }} <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $item?->nama) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400"
                    placeholder="Masukkan nama {{ strtolower($label) }}">
                @error('nama')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md transition-all">
                    {{ $item ? 'Perbarui' : 'Simpan' }}
                </button>
                <a href="{{ route('admin.master-data.index', $entity) }}"
                    class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
@endsection

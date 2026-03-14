@extends('layouts.app')
@section('title', 'Tambah Pegawai')
@section('header', 'Tambah Pegawai')

@section('content')
<x-card class="max-w-4xl">
    <form method="POST" action="{{ route('pegawai.store') }}" enctype="multipart/form-data">
        @csrf
        @include('pegawai._form')
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <x-button type="submit" size="lg">Simpan</x-button>
            <x-button variant="secondary" size="lg" href="{{ route('pegawai.index') }}">Batal</x-button>
        </div>
    </form>
</x-card>
@endsection

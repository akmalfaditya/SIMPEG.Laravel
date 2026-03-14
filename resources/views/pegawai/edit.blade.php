@extends('layouts.app')
@section('title', 'Edit Pegawai')
@section('header', 'Edit Pegawai')

@section('content')
<x-card class="max-w-4xl">
    <form method="POST" action="{{ route('pegawai.update', $pegawai) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('pegawai._form', ['pegawai' => $pegawai])
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <x-button type="submit" size="lg">Perbarui</x-button>
            <x-button variant="secondary" size="lg" href="{{ route('pegawai.show', $pegawai) }}">Batal</x-button>
        </div>
    </form>
</x-card>
@endsection

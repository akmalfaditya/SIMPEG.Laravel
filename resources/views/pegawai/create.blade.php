@extends('layouts.app')
@section('title', 'Tambah Pegawai')
@section('header', 'Tambah Pegawai')

@section('content')
<div class="max-w-4xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('pegawai.store') }}" enctype="multipart/form-data">
        @csrf
        @include('pegawai._form')
        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md transition-all">Simpan</button>
            <a href="{{ route('pegawai.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection

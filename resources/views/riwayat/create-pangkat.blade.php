@extends('layouts.app')
@section('title', 'Tambah Riwayat Pangkat')
@section('header', 'Tambah Riwayat Pangkat')
@section('content')
    <div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (isset($currentPangkat))
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                Pangkat saat ini: <strong>{{ $currentPangkat->golongan?->label }}</strong> (TMT:
                {{ $currentPangkat->tmt_pangkat->format('d/m/Y') }})
            </div>
        @endif
        @php $currentLevel = isset($currentPangkat) ? $currentPangkat->golongan?->golongan_ruang : 0; @endphp
        <form method="POST" action="{{ route('riwayat.pangkat.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $pegawaiId }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Golongan Ruang *</label><select
                        name="golongan_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm searchable-select">
                        @foreach ($golonganOptions as $g)
                            <option value="{{ $g->id }}" {{ $g->golongan_ruang <= $currentLevel ? 'disabled' : '' }}
                                {{ (int) old('golongan_id') === $g->id ? 'selected' : '' }}>
                                {{ $g->label }}{{ $g->golongan_ruang <= $currentLevel ? ' ✗' : '' }}</option>
                        @endforeach
                    </select>
                    @error('golongan_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label><input type="text"
                        name="nomor_sk" value="{{ old('nomor_sk') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Pangkat *<x-tooltip text="Terhitung Mulai Tanggal kenaikan pangkat" /></label><input type="date"
                        name="tmt_pangkat" value="{{ old('tmt_pangkat', today()->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK *</label><input type="date"
                        name="tanggal_sk" value="{{ old('tanggal_sk', today()->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload SK (PDF, maks 5MB)</label><input
                        type="file" name="file_sk" accept=".pdf"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label><input
                        type="url" name="google_drive_link" value="{{ old('google_drive_link') }}"
                        placeholder="https://drive.google.com/..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            </div>
            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">Simpan</button>
                <a href="{{ route('pegawai.show', $pegawaiId) }}"
                    class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Batal</a>
            </div>
        </form>
    </div>
@endsection

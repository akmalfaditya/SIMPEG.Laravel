@extends('layouts.app')
@section('title', 'Edit Riwayat Pangkat')
@section('header', 'Edit Riwayat Pangkat')
@section('content')
    <div class="max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('riwayat.pangkat.update', $riwayat) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Golongan Ruang *</label><select
                        name="golongan_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        @foreach ($golonganOptions as $g)
                            <option value="{{ $g->id }}" {{ $riwayat->golongan_id == $g->id ? 'selected' : '' }}>
                                {{ $g->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK</label><input type="text"
                        name="nomor_sk" value="{{ $riwayat->nomor_sk }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">TMT Pangkat *</label><input type="date"
                        name="tmt_pangkat" value="{{ $riwayat->tmt_pangkat->format('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal SK *</label><input type="date"
                        name="tanggal_sk" value="{{ $riwayat->tanggal_sk->format('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload SK (PDF, maks 5MB)</label><input
                        type="file" name="file_sk" accept=".pdf"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
                    @if ($riwayat->file_pdf_path)
                        <p class="text-xs text-green-600 mt-1">File SK sudah ada. Upload baru akan mengganti file lama.</p>
                    @endif
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label><input
                        type="url" name="google_drive_link" value="{{ $riwayat->google_drive_link }}"
                        placeholder="https://drive.google.com/..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            </div>
            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700">Perbarui</button>
                <a href="{{ route('pegawai.show', $riwayat->pegawai_id) }}"
                    class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Batal</a>
            </div>
        </form>
    </div>
@endsection

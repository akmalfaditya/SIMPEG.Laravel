@extends('layouts.app')
@section('title', 'Edit Pendidikan')
@section('header', 'Edit Riwayat Pendidikan')
@section('content')
    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('riwayat.pendidikan.update', $riwayat) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Pendidikan *</label><select
                        name="pendidikan_id" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        @foreach ($pendidikanList as $p)
                            <option value="{{ $p?->id }}" {{ $riwayat->pendidikan_id == $p?->id ? 'selected' : '' }}>
                                {{ $p?->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Institusi *</label><input type="text"
                        name="institusi" value="{{ $riwayat->institusi }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Jurusan *</label><input type="text"
                        name="jurusan" value="{{ $riwayat->jurusan }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun Lulus *</label><input type="number"
                        name="tahun_lulus" value="{{ $riwayat->tahun_lulus }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">No Ijazah</label><input type="text"
                        name="no_ijazah" value="{{ $riwayat->no_ijazah }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Ijazah</label><input
                        type="date" name="tanggal_ijazah" value="{{ $riwayat->tanggal_ijazah?->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload Ijazah/SK (PDF, maks
                        5MB)</label><input type="file" name="file_sk" accept=".pdf"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
                    @if ($riwayat->file_pdf_path)
                        <p class="text-xs text-green-600 mt-1">File sudah ada. Upload baru akan mengganti file lama. <a
                                href="{{ route('dokumen.download', ['type' => 'pendidikan', 'id' => $riwayat->id]) }}"
                                target="_blank" class="text-blue-600 hover:underline font-medium">Lihat Dokumen</a></p>
                    @endif
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Google Drive Link</label><input
                        type="url" name="google_drive_link" value="{{ $riwayat->google_drive_link }}"
                        placeholder="https://drive.google.com/..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
            </div>
            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <x-button type="submit" size="lg">Perbarui</x-button>
                <x-button variant="secondary" size="lg" href="{{ route('pegawai.show', $riwayat->pegawai_id) }}">Batal</x-button>
            </div>
        </form>
    </x-card>
@endsection

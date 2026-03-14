@extends('layouts.app')
@section('title', 'Edit SKP')
@section('header', 'Edit Penilaian Kinerja (SKP)')
@section('content')
    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('riwayat.skp.update', $riwayat) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun *</label><input type="number"
                        name="tahun" value="{{ $riwayat->tahun }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Nilai SKP *</label><select
                        name="nilai_skp" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Buruk'] as $v)
                            <option value="{{ $v }}" {{ $riwayat->nilai_skp == $v ? 'selected' : '' }}>
                                {{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Upload Dokumen SKP (PDF, maks
                        5MB)</label><input type="file" name="file_sk" accept=".pdf"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-600">
                    @if ($riwayat->file_pdf_path)
                        <p class="text-xs text-green-600 mt-1">File sudah ada. Upload baru akan mengganti file lama. <a
                                href="{{ route('dokumen.download', ['type' => 'skp', 'id' => $riwayat->id]) }}"
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

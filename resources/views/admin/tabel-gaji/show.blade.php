@extends('layouts.app')
@section('title', 'Tabel Gaji ' . $golongan->label)
@section('header', 'Tabel Gaji Golongan ' . $golongan->label)
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <a
        href="{{ route('admin.tabel-gaji.index') }}" class="hover:text-blue-600">Tabel Gaji</a> / <span
        class="text-slate-700">{{ $golongan->label }}</span>
@endsection
@section('content')
    <div class="space-y-6">
        {{-- Navigation antar golongan --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach ($allGolongan as $g)
                <a href="{{ route('admin.tabel-gaji.show', $g->id) }}"
                    class="px-2.5 py-1 text-xs rounded-lg transition-all {{ $g->id === $golongan->id ? 'bg-blue-600 text-white font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $g->label }}
                </a>
            @endforeach
        </div>

        {{-- Tabel MKG --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Daftar Gaji Pokok — {{ $golongan->label }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $entries->count() }} entri MKG</p>
                </div>
                <button onclick="document.getElementById('add-form').classList.toggle('hidden')"
                    class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah MKG
                </button>
            </div>

            {{-- Form tambah --}}
            <div id="add-form" class="hidden p-5 border-b border-slate-100 bg-slate-50">
                <form method="POST" action="{{ route('admin.tabel-gaji.store') }}" class="flex items-end gap-4 flex-wrap">
                    @csrf
                    <input type="hidden" name="golongan_id" value="{{ $golongan->id }}">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Masa Kerja (Tahun)</label>
                        <input type="number" name="masa_kerja_tahun" min="0" max="40" required
                            value="{{ old('masa_kerja_tahun') }}"
                            class="w-32 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('masa_kerja_tahun')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Gaji Pokok (Rp)</label>
                        <input type="number" name="gaji_pokok" min="0" required value="{{ old('gaji_pokok') }}"
                            class="w-44 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('gaji_pokok')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">Simpan</button>
                </form>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', () => document.getElementById('add-form').classList.remove('hidden'));
                </script>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-20">
                                MKG</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Gaji Pokok (Rp)</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-48">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-slate-50 transition-colors group" id="row-{{ $entry->id }}">
                                <td class="px-4 py-2.5 text-center font-medium text-slate-700">
                                    {{ $entry->masa_kerja_tahun }} th</td>
                                <td class="px-4 py-2.5 text-right font-mono text-slate-700">
                                    <span id="display-{{ $entry->id }}">Rp
                                        {{ number_format($entry->gaji_pokok, 0, ',', '.') }}</span>
                                    <form method="POST" action="{{ route('admin.tabel-gaji.update', $entry) }}"
                                        id="edit-form-{{ $entry->id }}" class="hidden items-center justify-end gap-2">
                                        @csrf @method('PUT')
                                        <input type="number" name="gaji_pokok" value="{{ intval($entry->gaji_pokok) }}"
                                            min="0"
                                            class="w-44 px-2 py-1 text-sm text-right border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <button type="submit"
                                            class="px-2 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Simpan</button>
                                        <button type="button" onclick="toggleEdit({{ $entry->id }})"
                                            class="px-2 py-1 bg-slate-200 text-slate-600 text-xs rounded-md hover:bg-slate-300">Batal</button>
                                    </form>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="toggleEdit({{ $entry->id }})"
                                            class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-600 hover:bg-amber-100 text-xs rounded-md font-medium transition-colors">Edit</button>
                                        <form method="POST" action="{{ route('admin.tabel-gaji.destroy', $entry) }}"
                                            onsubmit="return confirm('Hapus entri MKG {{ $entry->masa_kerja_tahun }} tahun?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-400">Belum ada data MKG untuk
                                    golongan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function toggleEdit(id) {
            const display = document.getElementById('display-' + id);
            const form = document.getElementById('edit-form-' + id);
            const isHidden = form.classList.contains('hidden');
            display.classList.toggle('hidden', isHidden);
            form.classList.toggle('hidden', !isHidden);
            if (isHidden) form.classList.add('flex');
            else form.classList.remove('flex');
        }
    </script>
@endpush

@extends('layouts.app')
@section('title', 'Master Golongan & Pangkat')
@section('header', 'Master Golongan & Pangkat PNS')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Master Golongan
        & Pangkat</span>
@endsection
@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-sm text-slate-500">Hierarki golongan/ruang dan pangkat PNS berdasarkan peraturan KEMENIPAS.
                    SuperAdmin dapat mengelola nama pangkat, pendidikan minimum, dan status aktif.</p>
            </div>
            <a href="{{ route('admin.golongan.create') }}"
                class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-12">
                            No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Golongan/Ruang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pangkat
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Min.
                            Pendidikan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Jumlah MKG</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Gaji
                            Min</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Gaji
                            Maks</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $group = ''; @endphp
                    @foreach ($golonganList as $idx => $row)
                        @php $currentGroup = $row->golongan_group; @endphp
                        @if ($currentGroup !== $group)
                            @php $group = $currentGroup; @endphp
                            <tr class="bg-slate-50/50">
                                <td colspan="9"
                                    class="px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Golongan
                                    {{ $group }}</td>
                            </tr>
                        @endif
                        <tr class="hover:bg-slate-50 transition-colors {{ !$row->is_active ? 'opacity-50' : '' }}">
                            <td class="px-4 py-2.5 text-center text-slate-400 text-xs">{{ $row->golongan_ruang }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $row->label }}</td>
                            <td class="px-4 py-2.5 text-slate-600">{{ $row->pangkat }}</td>
                            <td class="px-4 py-2.5 text-slate-600 text-xs">{{ $row->min_pendidikan ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-center text-slate-600">{{ $row->jumlah_mkg }}</td>
                            <td class="px-4 py-2.5 text-right font-mono text-slate-600">
                                @if ($row->gaji_min)
                                    Rp {{ number_format($row->gaji_min, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono text-slate-600">
                                @if ($row->gaji_max)
                                    Rp {{ number_format($row->gaji_max, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if ($row->is_active)
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full font-medium bg-red-100 text-red-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.golongan.edit', $row) }}"
                                        class="inline-flex items-center px-2 py-1 bg-amber-50 text-amber-600 hover:bg-amber-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('admin.golongan.toggle-active', $row) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center px-2 py-1 {{ $row->is_active ? 'bg-slate-50 text-slate-600 hover:bg-slate-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} text-xs rounded-md font-medium transition-colors">
                                            {{ $row->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.golongan.destroy', $row) }}"
                                        onsubmit="return confirm('Hapus golongan {{ $row->label }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            <p class="text-xs text-slate-400">* Data gaji pokok mengacu pada PP 15 Tahun 2019. Golongan/ruang standar I/a
                s.d. IV/e sesuai peraturan pemerintah.</p>
        </div>
    </div>
@endsection

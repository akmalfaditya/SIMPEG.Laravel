@extends('layouts.app')
@section('title', 'Tabel Gaji PNS')
@section('header', 'Master Tabel Gaji PNS')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Tabel
        Gaji</span>
@endsection
@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100">
            <p class="text-sm text-slate-500">Matriks gaji pokok PNS berdasarkan PP 15/2019. Klik golongan untuk melihat &
                mengelola detail per MKG.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Golongan/Ruang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Jumlah MKG</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Gaji
                            Pokok Min</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Gaji
                            Pokok Maks</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($summary as $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row->golongan->label }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $row->jumlah_mkg }} entri</td>
                            <td class="px-4 py-3 text-right font-mono text-slate-600">Rp
                                {{ number_format($row->gaji_min, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-slate-600">Rp
                                {{ number_format($row->gaji_max, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.tabel-gaji.show', $row->golongan->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs rounded-lg font-medium transition-colors">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">Data tabel gaji belum tersedia.
                                Jalankan seeder terlebih dahulu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

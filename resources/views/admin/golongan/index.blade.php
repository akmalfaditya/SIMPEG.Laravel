@extends('layouts.app')
@section('title', 'Referensi Golongan & Pangkat')
@section('header', 'Referensi Golongan & Pangkat PNS')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Referensi Golongan</span>
@endsection
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100">
        <p class="text-sm text-slate-500">Hierarki golongan/ruang dan pangkat PNS. Data ini mengacu pada peraturan pemerintah yang berlaku dan bersifat hanya-baca.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Golongan/Ruang</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pangkat</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Jumlah MKG</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Gaji Pokok Min</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Gaji Pokok Maks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $group = ''; @endphp
                @foreach($golonganList as $idx => $row)
                    @php $currentGroup = substr($row->label, 0, strpos($row->label, '/')); @endphp
                    @if($currentGroup !== $group)
                        @php $group = $currentGroup; @endphp
                        <tr class="bg-slate-50/50">
                            <td colspan="6" class="px-4 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Golongan {{ $group }}</td>
                        </tr>
                    @endif
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-2.5 text-center text-slate-400 text-xs">{{ $row->value }}</td>
                        <td class="px-4 py-2.5 font-medium text-slate-800">{{ $row->label }}</td>
                        <td class="px-4 py-2.5 text-slate-600">{{ $row->pangkat }}</td>
                        <td class="px-4 py-2.5 text-center text-slate-600">{{ $row->jumlah_mkg }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-slate-600">
                            @if($row->gaji_min) Rp {{ number_format($row->gaji_min, 0, ',', '.') }} @else <span class="text-slate-300">—</span> @endif
                        </td>
                        <td class="px-4 py-2.5 text-right font-mono text-slate-600">
                            @if($row->gaji_max) Rp {{ number_format($row->gaji_max, 0, ',', '.') }} @else <span class="text-slate-300">—</span> @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">
        <p class="text-xs text-slate-400">* Data gaji pokok mengacu pada PP 15 Tahun 2019. Golongan/ruang ditetapkan oleh peraturan pemerintah dan tidak dapat diubah melalui aplikasi.</p>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Master Jabatan')
@section('header', 'Master Data Jabatan')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Master Jabatan</span>
@endsection
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-center gap-2 flex-wrap flex-1">
            {{-- Filter Rumpun --}}
            <a href="{{ route('admin.jabatan.index', array_filter(['search' => $filterSearch])) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $filterRumpun === null ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
            @foreach($rumpunList as $r)
                <a href="{{ route('admin.jabatan.index', array_filter(['rumpun' => $r->value, 'search' => $filterSearch])) }}" class="px-3 py-1.5 text-xs rounded-lg {{ (int) $filterRumpun === $r->value ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $r->label() }}</a>
            @endforeach
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.jabatan.index') }}" class="flex items-center gap-2">
                @if($filterRumpun !== null) <input type="hidden" name="rumpun" value="{{ $filterRumpun }}"> @endif
                <input type="text" name="search" value="{{ $filterSearch }}" placeholder="Cari nama jabatan..." class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
                <button type="submit" class="px-3 py-1.5 text-xs bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200">Cari</button>
            </form>
            <a href="{{ route('admin.jabatan.create') }}" class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Jabatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Rumpun</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">BUP</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Eselon</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Kelas</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($jabatans as $j)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-2.5 font-medium text-slate-800">{{ $j->nama_jabatan }}</td>
                    <td class="px-4 py-2.5 text-slate-600">{{ $j->jenis_jabatan->label() }}</td>
                    <td class="px-4 py-2.5">
                        @php $color = $j->rumpun->color(); @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full font-medium bg-{{ $color }}-100 text-{{ $color }}-700">{{ $j->rumpun->label() }}</span>
                    </td>
                    <td class="px-4 py-2.5 text-center text-slate-600">{{ $j->bup }} th</td>
                    <td class="px-4 py-2.5 text-center text-slate-600">{{ $j->eselon_level ?: '—' }}</td>
                    <td class="px-4 py-2.5 text-center text-slate-600">{{ $j->kelas_jabatan }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.jabatan.edit', $j) }}" class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-600 hover:bg-amber-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                            <form method="POST" action="{{ route('admin.jabatan.destroy', $j) }}" onsubmit="return confirm('Hapus jabatan {{ $j->nama_jabatan }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Tidak ada data jabatan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jabatans->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $jabatans->links() }}
    </div>
    @endif
</div>
@endsection

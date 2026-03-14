@extends('layouts.app')
@section('title', 'Master ' . $label)
@section('header', 'Master ' . $label)
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> / <span class="text-slate-700">Master
        {{ $label }}</span>
@endsection
@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <p class="text-sm text-slate-500">Kelola data master <strong>{{ $label }}</strong>. Data ini digunakan
                sebagai pilihan dropdown pada formulir pegawai.</p>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('admin.master-data.index', $entity) }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..."
                        class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-blue-800 w-40">
                </form>
                <a href="{{ route('admin.master-data.create', $entity) }}"
                    class="px-3 py-1.5 bg-blue-800 hover:bg-blue-900 text-white text-xs font-medium rounded-lg transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">
                            No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama
                        </th>
                        @if ($entity === 'tingkat-pendidikan')
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Bobot DUK</th>
                        @endif
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-40">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-2.5 text-center text-slate-400 text-xs">
                                {{ $items->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $row->nama }}</td>
                            @if ($entity === 'tingkat-pendidikan')
                                <td class="px-4 py-2.5 text-center text-slate-600 font-bold bg-slate-50 rounded">{{ $row->bobot }}</td>
                            @endif
                            <td class="px-4 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.master-data.edit', [$entity, $row->id]) }}"
                                        class="inline-flex items-center px-2 py-1 bg-amber-50 text-amber-600 hover:bg-amber-100 text-xs rounded-md font-medium transition-colors">Edit</a>
                                    <button type="button"
                                        onclick="confirmDelete('{{ route('admin.master-data.destroy', [$entity, $row->id]) }}', 'Hapus {{ $label }} &quot;{{ $row->nama }}&quot;? Data pegawai yang menggunakan referensi ini mungkin terpengaruh.')"
                                        class="inline-flex items-center px-2 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs rounded-md font-medium transition-colors">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $entity === 'tingkat-pendidikan' ? '4' : '3' }}" class="px-4 py-8 text-center text-slate-400 text-sm">Belum ada data
                                {{ strtolower($label) }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
            <span class="text-xs text-slate-500">Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }}
                dari {{ $items->total() }} data</span>
            {{ $items->links() }}
        </div>
    </div>
@endsection

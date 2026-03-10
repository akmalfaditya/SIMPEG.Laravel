@extends('layouts.app')
@section('title', 'Activity Log')
@section('header', 'Riwayat Aktivitas')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a>
    <span class="text-slate-400">/</span>
    <span class="text-slate-600">Riwayat Aktivitas</span>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <p class="text-sm text-slate-500">Log aktivitas pengguna pada sistem.</p>
            <form method="GET" action="{{ route('activity-log.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..."
                    class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Subjek</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 text-xs text-slate-500">{{ $activity->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2.5 font-medium text-slate-700">{{ $activity->causer?->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-2.5">
                                <span
                                    class="px-2 py-1 text-xs rounded-full font-medium
                            {{ $activity->event === 'created' ? 'bg-emerald-100 text-emerald-700' : ($activity->event === 'updated' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($activity->event ?? $activity->description) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-xs">{{ class_basename($activity->subject_type ?? '-') }}
                                #{{ $activity->subject_id ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-xs text-slate-600">{{ $activity->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada aktivitas tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($activities->hasPages())
            <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-2 justify-between">
                <span class="text-xs text-slate-500">Menampilkan
                    {{ $activities->firstItem() ?? 0 }}–{{ $activities->lastItem() ?? 0 }} dari
                    {{ $activities->total() }}</span>
                {{ $activities->links() }}
            </div>
        @endif
    </div>
@endsection

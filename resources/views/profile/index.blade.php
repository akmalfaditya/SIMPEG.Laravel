@extends('layouts.app')
@section('title', 'Profil')
@section('header', 'Profil Saya')
@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a>
<span class="text-slate-400">/</span>
<span class="text-slate-600">Profil</span>
@endsection

@section('content')
<div class="max-w-2xl space-y-6">
    {{-- Info User --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Informasi Akun</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-400 text-xs">Nama</p>
                <p class="text-slate-700 font-medium">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Email</p>
                <p class="text-slate-700 font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Role</p>
                <p class="text-slate-700 font-medium">{{ $user->role ?? 'User' }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Bergabung Sejak</p>
                <p class="text-slate-700 font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Ubah Password</h3>
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Lama *</label>
                    <input type="password" name="current_password" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800">
                    @error('current_password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru *</label>
                    <input type="password" name="password" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800">
                    @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru *</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800">
                </div>
            </div>
            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <x-button type="submit" size="lg">Ubah Password</x-button>
            </div>
        </form>
    </div>
</div>
@endsection

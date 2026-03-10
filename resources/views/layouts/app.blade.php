<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMPEG') - Sistem Informasi Manajemen Pegawai</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="h-full bg-slate-50 font-[Inter]">
    <div class="flex h-full">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden" onclick="toggleSidebar()">
        </div>

        {{-- Sidebar --}}
        <aside id="sidebar"
            class="w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white flex flex-col shadow-2xl fixed h-full z-30 transition-all duration-300"
            style="transform: translateX(-100%);">
            <div class="p-5 border-b border-slate-700/60 flex items-center justify-between">
                <div>
                    <h1
                        class="text-xl font-bold tracking-wide bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                        SIMPEG</h1>
                    <p class="text-[11px] text-slate-400 mt-0.5">Sistem Informasi Manajemen Pegawai</p>
                </div>
                <button class="md:hidden text-slate-400 hover:text-white" onclick="toggleSidebar()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('pegawai.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('pegawai.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Data Pegawai
                </a>

                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest">Laporan</p>
                </div>

                <a href="{{ route('kgb.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('kgb.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    KGB
                </a>
                <a href="{{ route('kenaikan-pangkat.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('kenaikan-pangkat.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Kenaikan Pangkat
                </a>
                <a href="{{ route('pensiun.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('pensiun.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pensiun
                </a>
                <a href="{{ route('duk.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('duk.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    DUK
                </a>
                <a href="{{ route('satyalencana.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('satyalencana.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Satyalencana
                </a>

                @if (Auth::user()->role === 'SuperAdmin')
                    <div class="pt-3 pb-1 px-3">
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest">Admin Setting</p>
                    </div>

                    <a href="{{ route('admin.tabel-gaji.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.tabel-gaji.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tabel Gaji
                    </a>
                    <a href="{{ route('admin.golongan.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.golongan.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Master Golongan
                    </a>
                    <a href="{{ route('admin.jabatan.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.jabatan.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 13.255A23.193 23.193 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Master Jabatan
                    </a>

                    <div class="pt-2 pb-1 px-3">
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest">Master Data Pegawai</p>
                    </div>
                    @php
                        $masterEntities = [
                            'tipe-pegawai'       => 'Tipe Pegawai',
                            'status-kepegawaian' => 'Status Kepegawaian',
                            'bagian'             => 'Bagian',
                            'unit-kerja'         => 'Unit Kerja',
                            'jenis-kelamin'      => 'Jenis Kelamin',
                            'agama'              => 'Agama',
                            'status-pernikahan'  => 'Status Pernikahan',
                            'golongan-darah'     => 'Golongan Darah',
                        ];
                    @endphp
                    @foreach($masterEntities as $slug => $name)
                        <a href="{{ route('admin.master-data.index', $slug) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->is('admin/master-data/' . $slug . '*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            {{ $name }}
                        </a>
                    @endforeach
                @endif

                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest">Lainnya</p>
                </div>

                <a href="{{ route('activity-log.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('activity-log.*') ? 'bg-blue-600/30 text-blue-300 font-medium' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Log Aktivitas
                </a>
            </nav>
            <div class="p-4 border-t border-slate-700/60">
                <a href="{{ route('profile.show') }}"
                    class="flex items-center gap-3 mb-3 px-1 py-1 rounded-lg hover:bg-slate-700/50 transition-all">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-xs font-bold">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-[10px] text-slate-400">{{ Auth::user()->role ?? 'User' }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-xs text-slate-400 hover:text-red-400 hover:bg-slate-700/50 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 md:ml-64 overflow-y-auto">
            <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-lg border-b border-slate-200 px-4 md:px-6 py-4">
                <div class="flex items-center gap-3">
                    <button class="md:hidden text-slate-600 hover:text-slate-900" onclick="toggleSidebar()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">@yield('header', 'Dashboard')</h2>
                        @hasSection('breadcrumb')
                            <nav class="text-xs text-slate-500 mt-0.5">@yield('breadcrumb')</nav>
                        @endif
                    </div>
                </div>
            </header>
            <div class="p-4 md:p-6">
                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-300 rounded-xl flash-toast"
                        role="alert">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-emerald-800">Berhasil!</p>
                                <p class="text-sm text-emerald-700 mt-0.5">{{ session('success') }}</p>
                            </div>
                            <button type="button" onclick="this.closest('.flash-toast').remove()"
                                class="text-emerald-400 hover:text-emerald-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-300 rounded-xl flash-toast" role="alert">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-800">Terjadi Kesalahan!</p>
                                <p class="text-sm text-red-700 mt-0.5">{{ session('error') }}</p>
                            </div>
                            <button type="button" onclick="this.closest('.flash-toast').remove()"
                                class="text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm">
                        <ul class="list-disc list-inside text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
            <h3 class="text-lg font-semibold text-slate-800 mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-slate-600 mb-5" id="delete-modal-message">Apakah Anda yakin ingin menghapus data
                ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3 justify-end">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-300 hover:bg-slate-50 transition-all">Batal</button>
                <form id="delete-modal-form" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        // Sidebar: determine initial state based on screen width
        (function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth >= 768) {
                sidebar.style.transform = 'translateX(0)';
            }
        })();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isOpen = sidebar.style.transform === 'translateX(0px)' || sidebar.style.transform === 'translateX(0)';
            if (isOpen && window.innerWidth < 768) {
                sidebar.style.transform = 'translateX(-100%)';
                overlay.classList.add('hidden');
            } else {
                sidebar.style.transform = 'translateX(0)';
                overlay.classList.remove('hidden');
            }
        }

        // Handle resize: show sidebar on desktop, hide on mobile
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (window.innerWidth >= 768) {
                sidebar.style.transform = 'translateX(0)';
                overlay.classList.add('hidden');
            } else {
                sidebar.style.transform = 'translateX(-100%)';
                overlay.classList.add('hidden');
            }
        });

        // Auto-dismiss flash messages
        document.querySelectorAll('.flash-toast').forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }, 4000);
        });

        // Delete confirmation modal
        function confirmDelete(url, message) {
            document.getElementById('delete-modal').classList.remove('hidden');
            document.getElementById('delete-modal-form').action = url;
            if (message) document.getElementById('delete-modal-message').textContent = message;
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }
    </script>
</body>

</html>

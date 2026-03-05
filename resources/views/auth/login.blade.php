<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMPEG</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 font-[Inter] flex items-center justify-center">
    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">SIMPEG</h1>
            <p class="text-slate-400 text-sm mt-2">Sistem Informasi Manajemen Pegawai</p>
        </div>
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/10 p-8 shadow-2xl">
            <h2 class="text-xl font-semibold text-white mb-6">Masuk ke Akun</h2>
            @if($errors->any())
            <div class="mb-4 p-3 bg-red-500/20 border border-red-400/30 rounded-lg text-red-300 text-sm">
                @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
                </div>
                <div class="flex items-center mb-6">
                    <input type="checkbox" name="remember" id="remember" class="rounded bg-white/10 border-white/20 text-blue-500 focus:ring-blue-500/50">
                    <label for="remember" class="ml-2 text-sm text-slate-400">Ingat saya</label>
                </div>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.02] transition-all duration-200">
                    Masuk
                </button>
            </form>
        </div>
        <p class="text-center text-slate-500 text-xs mt-6">Admin: admin@simpeg.go.id / password</p>
    </div>
</body>
</html>

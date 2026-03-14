<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMPEG</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-slate-50 font-[Inter] flex items-center justify-center">
    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-800">SIMPEG</h1>
            <p class="text-slate-500 text-sm mt-2">Sistem Informasi Manajemen Pegawai</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            <h2 class="text-xl font-semibold text-slate-900 mb-6">Masuk ke Akun</h2>
            @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-sm">
                @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800 transition-colors">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800 transition-colors">
                </div>
                <div class="flex items-center mb-6">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-blue-800 focus:ring-blue-800/50">
                    <label for="remember" class="ml-2 text-sm text-slate-600">Ingat saya</label>
                </div>
                <button type="submit" class="w-full py-2.5 bg-blue-800 text-white font-semibold rounded-lg hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-800 focus:ring-offset-2 transition-colors">
                    Masuk
                </button>
            </form>
        </div>
        <p class="text-center text-slate-400 text-xs mt-6">Admin: superadmin@kemenipas.go.id / password</p>
    </div>
</body>
</html>

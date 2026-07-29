<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-5">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-red-50 p-8 flex justify-center">
            <div class="w-24 h-24 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
        <div class="p-8 text-center">
            <h1 class="text-4xl font-black text-slate-800 mb-2">403</h1>
            <h2 class="text-xl font-bold text-slate-700 mb-4">Akses Ditolak</h2>
            <p class="text-slate-500 mb-8 text-sm">Maaf, Anda tidak memiliki izin atau *role* yang sesuai untuk mengakses halaman ini.</p>
            
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-colors">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>

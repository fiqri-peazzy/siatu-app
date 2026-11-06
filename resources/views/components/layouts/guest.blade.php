<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="siatu-versi" content="{{ \App\Support\VersiAplikasi::nilai() }}">

        <title>{{ config('app.name', 'SITU-SMA') }}</title>
        
        <link rel="icon" type="image/png" href="{{ asset('images/logo-removebg-preview.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-removebg-preview.png') }}">

        @include('components.layouts.partials.fonts')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>

            
            /* High-end Vibe: Ethereal Glass / Soft Structuralism */
            .ethereal-bg {
                background-color: #050505;
                background-image: 
                    radial-gradient(circle at 15% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 85% 30%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
                background-attachment: fixed;
            }
            
            .glass-panel {
                background: rgba(15, 15, 15, 0.6);
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
            }
        </style>
    </head>
    <body class="antialiased ethereal-bg text-slate-200 selection:bg-indigo-500/30">
        
        <!-- Subtle noise overlay for texture -->
        <div class="pointer-events-none fixed inset-0 z-50 h-full w-full opacity-[0.03] mix-blend-overlay" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

        <div class="min-h-[100dvh] flex flex-col items-center justify-center p-4 py-24 sm:p-6 md:p-12 relative z-10">
            {{ $slot }}
        </div>
    </body>
</html>

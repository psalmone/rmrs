<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Malaptw RiceMill System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            }
            .auth-bg {
                background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.15) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(245, 158, 11, 0.15) 0%, transparent 40%),
                            #050c07;
            }
            .grain-bg {
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
            }
            .pattern-grid {
                background-image: radial-gradient(rgba(245, 158, 11, 0.12) 1.5px, transparent 1.5px);
                background-size: 24px 24px;
            }
            .glass-card {
                background: rgba(13, 23, 16, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(245, 158, 11, 0.22);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 30px -5px rgba(245, 158, 11, 0.15);
            }
        </style>

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full auth-bg text-slate-100 antialiased selection:bg-amber-500 selection:text-slate-950 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-x-hidden">
        
        <!-- Ambient Grid & Grain -->
        <div class="fixed inset-0 pattern-grid opacity-50 pointer-events-none"></div>
        <div class="fixed inset-0 grain-bg pointer-events-none"></div>

        <!-- Glowing Spheres -->
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10 my-8">
            <!-- Branding Header -->
            <div class="text-center mb-6">
                <a href="/" class="inline-flex items-center justify-center p-1 rounded-2xl bg-gradient-to-tr from-amber-500 via-amber-400 to-yellow-300 shadow-xl shadow-amber-500/20 mb-3 hover:scale-105 transition-transform duration-200">
                    <div class="w-12 h-12 bg-[#09150c] rounded-[14px] flex items-center justify-center">
                        <x-application-logo class="w-7 h-7 text-amber-400" />
                    </div>
                </a>
                <div class="flex items-center justify-center gap-2">
                    <h1 class="text-2xl font-black tracking-tight text-white">Malaptw</h1>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">RiceMill</span>
                </div>
                <p class="text-[11px] uppercase tracking-widest font-semibold text-slate-400 mt-1">Management Information System</p>
            </div>

            <!-- Glassmorphism Main Card -->
            <div class="glass-card rounded-3xl p-6 sm:p-8">
                {{ $slot }}
            </div>

            <!-- Footer note & Home Link -->
            <div class="text-center mt-6 space-y-2">
                <a href="/" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-amber-300 transition-colors font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Home Overview</span>
                </a>
                <p class="text-slate-500 text-[11px]">
                    &copy; {{ date('Y') }} Malaptw RiceMill System &bull; Secure Enterprise Portal
                </p>
            </div>
        </div>
    </body>
</html>

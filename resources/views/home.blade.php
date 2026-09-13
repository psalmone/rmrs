<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Malaptw RiceMill System — Enterprise Operations & Management</title>
    <meta name="description" content="Malaptw RiceMill System — Manage rice milling operations, raw palay intake, milled rice inventory, sales, and analytics in one unified platform.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .grain-bg {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
        }
        .mesh-gradient {
            background: radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.18) 0px, transparent 50%),
                        radial-gradient(at 100% 0%, rgba(245, 158, 11, 0.22) 0px, transparent 50%),
                        radial-gradient(at 50% 100%, rgba(5, 150, 105, 0.12) 0px, transparent 50%),
                        #050c07;
        }
        .dot-pattern {
            background-image: radial-gradient(rgba(245, 158, 11, 0.12) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }
        .glass-panel {
            background: rgba(13, 23, 16, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(245, 158, 11, 0.15);
        }
        .glass-panel-hover {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glass-panel-hover:hover {
            transform: translateY(-4px);
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6), 0 0 25px -5px rgba(245, 158, 11, 0.2);
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1.5deg); }
        }
        .animate-float {
            animation: float-slow 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased bg-[#050c07] text-slate-100 selection:bg-amber-500 selection:text-slate-950 relative overflow-x-hidden min-h-screen">

    <!-- Ambient Gradients -->
    <div class="fixed inset-0 dot-pattern pointer-events-none opacity-40 z-0"></div>
    <div class="fixed inset-0 grain-bg pointer-events-none z-0"></div>

    <!-- ====== NAVBAR ====== -->
    <header class="fixed top-0 left-0 right-0 z-50 border-b border-white/5 bg-[#050c07]/80 backdrop-blur-xl transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Logo & Brand Title -->
            <a href="/" class="flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-500 via-amber-400 to-yellow-300 p-0.5 shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform duration-300">
                    <div class="w-full h-full bg-[#09150c] rounded-[14px] flex items-center justify-center">
                        <x-application-logo class="w-6 h-6 text-amber-400" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-black tracking-tight text-white group-hover:text-amber-400 transition-colors">Malaptw</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">RiceMill</span>
                    </div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Enterprise Operations Portal</p>
                </div>
            </a>

            <!-- Navigation Actions -->
            <div class="flex items-center gap-3 sm:gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm rounded-xl transition duration-200 shadow-lg shadow-amber-500/20 active:scale-95">
                        <span>Management Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold text-slate-300 hover:text-white px-4 py-2 rounded-xl hover:bg-white/5 transition duration-150">
                        Sign In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm rounded-xl transition duration-200 shadow-lg shadow-amber-500/20 active:scale-95">
                            <span>Register Staff</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main class="relative z-10 pt-20">
        <!-- ====== HERO SECTION ====== -->
        <section class="mesh-gradient relative overflow-hidden py-16 sm:py-24 lg:py-28 border-b border-white/5">
            <!-- Decorative Light Spheres -->
            <div class="absolute top-1/4 -right-20 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    
                    <!-- Left: Hero Copy & CTA -->
                    <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                        <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider">Automated Rice Milling Operations</span>
                        </div>

                        <div class="space-y-3">
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.15]">
                                Precision Milling, <br>
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500">
                                    Total Stock Control
                                </span> <br>
                                In One Single System.
                            </h1>
                            <p class="text-base sm:text-lg text-slate-300 max-w-xl font-normal leading-relaxed pt-2">
                                Engineered specifically for <strong class="text-amber-300 font-semibold">Malaptw RiceMill</strong>. Automate paddy intake weights, monitor milling yield recovery, track multi-variety rice sacks, and generate ledger-ready financial reports.
                            </p>
                        </div>

                        <!-- CTA Actions -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400 hover:brightness-110 text-slate-950 font-extrabold text-sm rounded-xl shadow-xl shadow-amber-500/25 transition duration-200 active:scale-[0.98]">
                                    <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    <span>Access Active Dashboard</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400 hover:brightness-110 text-slate-950 font-extrabold text-sm rounded-xl shadow-xl shadow-amber-500/25 transition duration-200 active:scale-[0.98]">
                                    <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    <span>Sign In to Mill Portal</span>
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-amber-400/40 text-white font-semibold text-sm rounded-xl transition duration-200">
                                        <span>Create Staff Account</span>
                                    </a>
                                @endif
                            @endauth
                        </div>

                        <!-- Highlights Banner -->
                        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-white/10 max-w-lg">
                            <div>
                                <p class="text-2xl sm:text-3xl font-black text-amber-400">100%</p>
                                <p class="text-xs text-slate-400 font-medium">Paperless Logs</p>
                            </div>
                            <div>
                                <p class="text-2xl sm:text-3xl font-black text-emerald-400">Real-Time</p>
                                <p class="text-xs text-slate-400 font-medium">Sack Inventory</p>
                            </div>
                            <div>
                                <p class="text-2xl sm:text-3xl font-black text-amber-400">Multi-Role</p>
                                <p class="text-xs text-slate-400 font-medium">Secure Access</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Interactive System Status Card -->
                    <div class="lg:col-span-5 relative">
                        <div class="animate-float">
                            <!-- Main Mill Monitor Card -->
                            <div class="glass-panel rounded-3xl p-6 sm:p-7 shadow-2xl relative overflow-hidden">
                                <div class="flex items-center justify-between pb-5 border-b border-white/10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></div>
                                        <div>
                                            <h3 class="text-sm font-bold text-white">Rice Mill Operations Center</h3>
                                            <p class="text-xs text-slate-400">Malaptw Facility &bull; Live Telemetry</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[11px] font-bold">ACTIVE</span>
                                </div>

                                <!-- Key Indicators -->
                                <div class="grid grid-cols-2 gap-3.5 my-5">
                                    <div class="bg-black/30 rounded-2xl p-3.5 border border-white/5">
                                        <p class="text-xs text-slate-400 font-medium">Palay Intake Today</p>
                                        <p class="text-xl font-black text-white mt-1">420 <span class="text-xs text-amber-400 font-normal">Sacks</span></p>
                                        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2">
                                            <div class="bg-amber-400 h-1.5 rounded-full" style="width: 78%"></div>
                                        </div>
                                    </div>
                                    <div class="bg-black/30 rounded-2xl p-3.5 border border-white/5">
                                        <p class="text-xs text-slate-400 font-medium">Milled Output Yield</p>
                                        <p class="text-xl font-black text-white mt-1">68.4% <span class="text-xs text-emerald-400 font-normal">Recovery</span></p>
                                        <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2">
                                            <div class="bg-emerald-400 h-1.5 rounded-full" style="width: 88%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Live Activity Feed -->
                                <div class="space-y-2.5">
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Recent System Logs</p>
                                    
                                    <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-white/5 border border-white/5">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                            <span class="text-slate-200">Batch #M-2026-88 Finished</span>
                                        </div>
                                        <span class="text-slate-400 font-mono text-[11px]">2m ago</span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-white/5 border border-white/5">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span class="text-slate-200">Wholesale Rice Sacks Released</span>
                                        </div>
                                        <span class="text-slate-400 font-mono text-[11px]">14m ago</span>
                                    </div>
                                </div>

                                <!-- By-Product Tracker Bar -->
                                <div class="mt-5 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
                                    <span>By-Products:</span>
                                    <div class="flex gap-2">
                                        <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-mono">Darak: 32 bags</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono">Ipa: 18 cu.m</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ====== MODULES / FEATURES SECTION ====== -->
        <section class="py-20 sm:py-28 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold uppercase tracking-wider">
                        Enterprise Modules
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4 tracking-tight">
                        Engineered for High-Volume Rice Milling
                    </h2>
                    <p class="text-slate-400 text-base mt-3">
                        Every tool necessary to operate from farmer intake to commercial wholesale distribution.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Feature 1: Raw Palay Intake -->
                    <div class="glass-panel glass-panel-hover rounded-3xl p-7 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Palay Intake & Weighing</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Record gross & tare weights, moisture deductions, price per kilo, and farmer producer records directly onto digital tickets.
                            </p>
                        </div>
                        <div class="pt-5 mt-5 border-t border-white/5 flex items-center text-xs text-amber-400 font-semibold">
                            <span>Includes ticket printing & deductions &rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 2: Milling Runs & Recovery -->
                    <div class="glass-panel glass-panel-hover rounded-3xl p-7 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Milling Batch Processing</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Monitor raw sack inputs against finished output sacks. Automatically calculate milling recovery % to maintain machinery efficiency.
                            </p>
                        </div>
                        <div class="pt-5 mt-5 border-t border-white/5 flex items-center text-xs text-emerald-400 font-semibold">
                            <span>Automated yield % calculation &rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 3: Milled Rice Inventory -->
                    <div class="glass-panel glass-panel-hover rounded-3xl p-7 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-yellow-500/15 border border-yellow-500/30 flex items-center justify-center text-yellow-400 mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Rice Stock & Varieties</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Track 25kg & 50kg sacks by variety (Sinandomeng, Dinorado, Well Milled, Premium, Broken Rice) with stock alerts.
                            </p>
                        </div>
                        <div class="pt-5 mt-5 border-t border-white/5 flex items-center text-xs text-yellow-400 font-semibold">
                            <span>Low-stock threshold alerts &rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 4: By-Products & Co-Products -->
                    <div class="glass-panel glass-panel-hover rounded-3xl p-7 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">By-Product Management</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Never lose revenue on co-products. Account for Rice Bran (Darak 1 & 2), Hull/Husk (Ipa), and Binlid sales seamlessly.
                            </p>
                        </div>
                        <div class="pt-5 mt-5 border-t border-white/5 flex items-center text-xs text-amber-400 font-semibold">
                            <span>Co-product ledger tracking &rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 5: Sales & Cashiering -->
                    <div class="glass-panel glass-panel-hover rounded-3xl p-7 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">POS & Invoicing</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Issue digital receipts for wholesale buyers and retail walk-ins. Support cash, GCash/bank transfers, and credit terms.
                            </p>
                        </div>
                        <div class="pt-5 mt-5 border-t border-white/5 flex items-center text-xs text-emerald-400 font-semibold">
                            <span>Wholesale & retail pricing tier &rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 6: Real-time Reports & Audit -->
                    <div class="glass-panel glass-panel-hover rounded-3xl p-7 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center text-blue-400 mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Financial Reports & Logs</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Daily reconciliation, operator performance metrics, revenue statements, and full audit logs for owner peace of mind.
                            </p>
                        </div>
                        <div class="pt-5 mt-5 border-t border-white/5 flex items-center text-xs text-blue-400 font-semibold">
                            <span>Exportable summaries & analytics &rarr;</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ====== CTA FOOTER BANNER ====== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <div class="glass-panel rounded-3xl p-8 sm:p-12 relative overflow-hidden text-center bg-gradient-to-r from-emerald-950/80 via-slate-900 to-amber-950/80 border border-amber-500/20">
                <div class="max-w-2xl mx-auto space-y-6">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                        Authorized Staff Access Only
                    </h2>
                    <p class="text-sm text-slate-300">
                        Log in using your official credentials to access mill operations, inventory control, and transaction records.
                    </p>
                    <div class="pt-2 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm rounded-xl shadow-lg shadow-amber-500/20 transition duration-200">
                            <span>Proceed to Login</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ====== FOOTER ====== -->
    <footer class="border-t border-white/10 bg-[#050c07] py-8 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 p-0.5">
                    <div class="w-full h-full bg-[#09150c] rounded-[10px] flex items-center justify-center">
                        <x-application-logo class="w-4 h-4 text-amber-400" />
                    </div>
                </div>
                <span class="text-sm font-bold text-slate-300">Malaptw RiceMill System</span>
            </div>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Malaptw RiceMill. Built with Laravel {{ app()->version() }}. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>

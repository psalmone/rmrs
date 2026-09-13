<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-white tracking-tight">
                    {{ __('Operations Management Dashboard') }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Welcome back, {{ Auth::user()->name }} &bull; Malapote Ricemill Plant</p>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Milling Active &bull; Database Connected
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- ====== LOW STOCK ALERT BANNER ====== -->
            @if(isset($lowStockItems) && $lowStockItems->isNotEmpty())
                <div class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-200 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="p-2.5 rounded-xl bg-rose-500/20 text-rose-400 flex-shrink-0 animate-bounce">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                Critical Low Stock Alert: {{ $lowStockItems->count() }} item(s) below reorder threshold
                            </h3>
                            <p class="text-xs text-rose-300/80 mt-0.5">
                                Items: 
                                @foreach($lowStockItems as $lItem)
                                    <span class="font-semibold text-white">{{ $lItem->name }} ({{ number_format($lItem->current_stock) }} {{ $lItem->unit }})</span>{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold text-xs shadow-lg shadow-rose-500/20 transition">
                            View Low Stock
                        </a>
                        <a href="{{ route('inventory.adjust.form') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-white font-semibold text-xs transition">
                            Adjust Stock
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- ====== TOP 4 KPI CARDS ====== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- KPI 1: Raw Palay Stock -->
                <div class="bg-[#0b160f]/85 backdrop-blur-xl border border-amber-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-amber-400/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-wider font-bold text-slate-400">Raw Palay Intake</span>
                        <span class="p-2.5 rounded-xl bg-amber-500/15 text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-white mt-3">
                        {{ number_format($totalPalaySacks ?? 0) }} <span class="text-xs font-normal text-amber-400">sacks</span>
                    </p>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-white/5 text-[11px] text-slate-400">
                        <span>Net Weight:</span>
                        <span class="text-slate-200 font-mono font-bold">{{ number_format($totalPalayWeightKg ?? 0, 1) }} kg</span>
                    </div>
                </div>

                <!-- KPI 2: Milled Rice Inventory -->
                <div class="bg-[#0b160f]/85 backdrop-blur-xl border border-emerald-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-emerald-400/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-wider font-bold text-slate-400">Milled Rice Inventory</span>
                        <span class="p-2.5 rounded-xl bg-emerald-500/15 text-emerald-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-white mt-3">
                        {{ number_format($milledRiceStock ?? 0) }} <span class="text-xs font-normal text-emerald-400">bags/sacks</span>
                    </p>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-white/5 text-[11px] text-slate-400">
                        <span>Varieties in Stock:</span>
                        <span class="text-emerald-400 font-semibold">Sinandomeng, Dinorado, WM</span>
                    </div>
                </div>

                <!-- KPI 3: Milling Yield Recovery Efficiency -->
                <div class="bg-[#0b160f]/85 backdrop-blur-xl border border-yellow-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-yellow-400/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-wider font-bold text-slate-400">Recovery Efficiency</span>
                        <span class="p-2.5 rounded-xl bg-yellow-500/15 text-yellow-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-white mt-3">
                        {{ $avgRecoveryRate ?? 0 }}%
                    </p>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-white/5 text-[11px] text-slate-400">
                        <span>Total Output Recovered:</span>
                        <span class="text-yellow-400 font-mono font-bold">{{ number_format($totalMilledOutputKg ?? 0, 1) }} kg</span>
                    </div>
                </div>

                <!-- KPI 4: Sales Revenue & Collectibles -->
                <div class="bg-[#0b160f]/85 backdrop-blur-xl border border-blue-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden group hover:border-blue-400/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-wider font-bold text-slate-400">Total Sales Invoiced</span>
                        <span class="p-2.5 rounded-xl bg-blue-500/15 text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-white mt-3">
                        ₱{{ number_format($totalSalesAmount ?? 0, 2) }}
                    </p>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-white/5 text-[11px] text-slate-400">
                        <span>Receivables / Credit:</span>
                        <span class="text-rose-400 font-mono font-bold">₱{{ number_format($outstandingBalances ?? 0, 2) }}</span>
                    </div>

                </div>
            </div>

            <!-- ====== LIVE TABLES / FEEDS ====== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Feed 1: Recent Palay Intake Tickets (Farmers/Suppliers) -->
                <div class="bg-[#0b160f]/85 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                Recent Palay Intake Tickets
                            </h3>
                            <p class="text-xs text-slate-400">Raw paddy received from farmers & suppliers</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            {{ ($recentIntakes ?? collect())->count() }} Tickets
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-white/10">
                                <tr>
                                    <th class="py-2.5 font-bold">Ticket #</th>
                                    <th class="py-2.5 font-bold">Farmer / Supplier</th>
                                    <th class="py-2.5 font-bold text-right">Bags</th>
                                    <th class="py-2.5 font-bold text-right">Net Weight</th>
                                    <th class="py-2.5 font-bold text-right">Total (₱)</th>
                                    <th class="py-2.5 font-bold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse (($recentIntakes ?? []) as $intake)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="py-3 font-mono font-bold text-amber-400">{{ $intake->ticket_number }}</td>
                                        <td class="py-3 text-white font-medium">{{ $intake->farmer_name }}</td>
                                        <td class="py-3 text-right font-mono">{{ $intake->bag_count }}</td>
                                        <td class="py-3 text-right font-mono">{{ number_format($intake->net_weight_kg, 1) }} kg</td>
                                        <td class="py-3 text-right font-mono font-semibold text-slate-100">₱{{ number_format($intake->total_amount, 2) }}</td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $intake->payment_status === 'paid' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/15 text-amber-400 border border-amber-500/20' }}">
                                                {{ $intake->payment_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-500">No intake tickets found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Feed 2: Recent Milling Batches & Recovery Efficiency -->
                <div class="bg-[#0b160f]/85 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                Milling Runs & Recovery Yield
                            </h3>
                            <p class="text-xs text-slate-400">Batch throughput and machinery recovery rates</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            {{ ($recentBatches ?? collect())->count() }} Batches
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-white/10">
                                <tr>
                                    <th class="py-2.5 font-bold">Batch #</th>
                                    <th class="py-2.5 font-bold">Variety</th>
                                    <th class="py-2.5 font-bold text-right">Input (kg)</th>
                                    <th class="py-2.5 font-bold text-right">Output (kg)</th>
                                    <th class="py-2.5 font-bold text-right">Yield %</th>
                                    <th class="py-2.5 font-bold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse (($recentBatches ?? []) as $batch)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="py-3 font-mono font-bold text-emerald-400">{{ $batch->batch_number }}</td>
                                        <td class="py-3 text-white font-medium">{{ $batch->palay_variety }}</td>
                                        <td class="py-3 text-right font-mono">{{ number_format($batch->input_weight_kg, 1) }}</td>
                                        <td class="py-3 text-right font-mono">{{ number_format($batch->total_milled_output_kg, 1) }}</td>
                                        <td class="py-3 text-right font-mono font-bold text-amber-400">{{ $batch->recovery_rate_pct }}%</td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">
                                                {{ $batch->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-500">No milling batches recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- ====== SALES & BY-PRODUCT INVENTORY ====== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sales Invoices (2 cols) -->
                <div class="lg:col-span-2 bg-[#0b160f]/85 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                                Wholesale & Retail Sales Invoices
                            </h3>
                            <p class="text-xs text-slate-400">Customer transactions, invoices & receivables</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-white/10">
                                <tr>
                                    <th class="py-2.5 font-bold">Invoice #</th>
                                    <th class="py-2.5 font-bold">Customer</th>
                                    <th class="py-2.5 font-bold">Type</th>
                                    <th class="py-2.5 font-bold text-right">Net Amount</th>
                                    <th class="py-2.5 font-bold text-right">Balance</th>
                                    <th class="py-2.5 font-bold text-center">Payment</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse (($recentSales ?? []) as $sale)

                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="py-3 font-mono font-bold text-blue-400">{{ $sale->invoice_number }}</td>
                                        <td class="py-3 text-white font-medium">{{ $sale->customer_name }}</td>
                                        <td class="py-3 uppercase text-[10px] font-semibold text-slate-400">{{ $sale->sale_type }}</td>
                                        <td class="py-3 text-right font-mono font-bold text-white">₱{{ number_format($sale->net_amount, 2) }}</td>
                                        <td class="py-3 text-right font-mono text-rose-400 font-semibold">₱{{ number_format($sale->balance_amount, 2) }}</td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sale->payment_status === 'paid' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' : 'bg-yellow-500/15 text-yellow-400 border border-yellow-500/20' }}">
                                                {{ $sale->payment_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-500">No sales transactions logged.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- By-Products & Inventory Summary (1 col) -->
                <div class="bg-[#0b160f]/85 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-xl space-y-4">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        By-Product Stocks on Hand
                    </h3>
                    <p class="text-xs text-slate-400">Co-products ready for feedmill/poultry buyers</p>

                    <div class="space-y-3 pt-2">
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">Rice Bran (Darak D1)</p>
                                <p class="text-[11px] text-slate-400">50kg bags for animal feeds</p>
                            </div>
                            <span class="text-sm font-black text-amber-400 font-mono">185 bags</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">Broken Rice (Binlid)</p>
                                <p class="text-[11px] text-slate-400">Brewers & poultry mash</p>
                            </div>
                            <span class="text-sm font-black text-emerald-400 font-mono">95 bags</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">Rice Hull (Ipa)</p>
                                <p class="text-[11px] text-slate-400">Bulk biomass & fuel</p>
                            </div>
                            <span class="text-sm font-black text-yellow-400 font-mono">240 sacks</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

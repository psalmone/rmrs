<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                    <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </span>
                    Inventory Control & Stock Management
                </h2>
                <p class="text-xs text-slate-400 mt-1">Manage rice grades, raw grain silos, by-products, manual adjustments, and stock levels.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('inventory.adjust.form') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-200 font-bold text-xs transition duration-150">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                    Stock Adjustment
                </a>
                <a href="{{ route('inventory.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Item / Grade
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Catalog Items</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($stats['total_items']) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-amber-500/20">
                    <p class="text-xs text-slate-400 font-medium">Raw Palay on Hand</p>
                    <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($stats['raw_palay_sacks']) }} <span class="text-xs font-normal text-slate-400">sacks</span></p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-emerald-500/20">
                    <p class="text-xs text-slate-400 font-medium">Milled Rice Stock</p>
                    <p class="text-2xl font-bold text-emerald-400 mt-1">{{ number_format($stats['milled_rice_sacks']) }} <span class="text-xs font-normal text-slate-400">sacks</span></p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-blue-500/20">
                    <p class="text-xs text-slate-400 font-medium">By-Products Stock</p>
                    <p class="text-2xl font-bold text-blue-400 mt-1">{{ number_format($stats['by_product_sacks']) }} <span class="text-xs font-normal text-slate-400">bags</span></p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border {{ $stats['low_stock_count'] > 0 ? 'border-rose-500/40 bg-rose-500/5' : 'border-white/10' }}">
                    <p class="text-xs text-slate-400 font-medium">Low Stock Alerts</p>
                    <p class="text-2xl font-bold {{ $stats['low_stock_count'] > 0 ? 'text-rose-400' : 'text-slate-400' }} mt-1">
                        {{ $stats['low_stock_count'] }} <span class="text-xs font-normal">items</span>
                    </p>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10">
                <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by Item Code, Product Name, Variety..." 
                            class="w-full pl-10 pr-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-amber-400">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="category" onchange="this.form.submit()" class="w-full py-2 px-3 rounded-xl bg-[#0d1c10] border border-white/10 text-slate-200 text-sm focus:outline-none focus:border-amber-400">
                            <option value="">All Categories</option>
                            <option value="raw_palay" {{ $category === 'raw_palay' ? 'selected' : '' }}>Raw Palay</option>
                            <option value="milled_rice" {{ $category === 'milled_rice' ? 'selected' : '' }}>Milled Rice</option>
                            <option value="by_product" {{ $category === 'by_product' ? 'selected' : '' }}>By-Products (Bran/Binlid)</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-44">
                        <select name="filter" onchange="this.form.submit()" class="w-full py-2 px-3 rounded-xl bg-[#0d1c10] border border-white/10 text-slate-200 text-sm focus:outline-none focus:border-amber-400">
                            <option value="">All Stock Levels</option>
                            <option value="low_stock" {{ $filter === 'low_stock' ? 'selected' : '' }}>Low Stock Alerts Only</option>
                        </select>
                    </div>
                    @if($search || $category || $filter)
                        <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs text-slate-300 font-semibold flex items-center justify-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Items Catalog Table -->
            <div class="rounded-2xl bg-white/[0.02] border border-white/10 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase bg-white/[0.04] text-slate-400 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-4">Item Code</th>
                                <th class="px-6 py-4">Product Name</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Package Unit</th>
                                <th class="px-6 py-4 text-right">Current Stock</th>
                                <th class="px-6 py-4 text-right">Reorder Level</th>
                                <th class="px-6 py-4 text-right">Unit Price</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($items as $item)
                                @php
                                    $isLow = (float) $item->current_stock <= (float) $item->reorder_level;
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition {{ $isLow ? 'bg-rose-500/[0.03]' : '' }}">
                                    <td class="px-6 py-4 font-mono font-bold text-amber-400">
                                        {{ $item->code }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-white">{{ $item->name }}</div>
                                        @if($item->variety)
                                            <span class="text-[11px] text-slate-400">Variety: {{ $item->variety }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs capitalize font-medium
                                            {{ $item->category === 'raw_palay' ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 
                                               ($item->category === 'milled_rice' ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : 
                                               'bg-blue-500/10 text-blue-300 border border-blue-500/20') }}">
                                            {{ str_replace('_', ' ', $item->category) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400 text-xs">
                                        {{ str_replace('_', ' ', $item->unit) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-lg {{ $isLow ? 'text-rose-400' : 'text-white' }}">
                                        {{ number_format($item->current_stock, 1) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-slate-400">
                                        {{ number_format($item->reorder_level, 1) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-semibold text-slate-200">
                                        ₱{{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($isLow)
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Healthy
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('inventory.adjust.form', ['item_id' => $item->id]) }}" class="p-1.5 rounded-lg hover:bg-white/10 text-amber-400" title="Adjust Stock">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('inventory.edit', $item) }}" class="p-1.5 rounded-lg hover:bg-white/10 text-slate-400 hover:text-white" title="Edit Item Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                        No items found matching your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="px-6 py-4 border-t border-white/10 bg-white/[0.01]">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

            <!-- Recent Stock Adjustments Audit Trail -->
            @if($recentAdjustments->isNotEmpty())
                <div class="rounded-2xl bg-white/[0.02] border border-white/10 p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">Stock Adjustment Audit Log</h3>
                            <p class="text-xs text-slate-400">Recent manual additions, spillages, bag damages, and shrinkage deductions</p>
                        </div>
                        <a href="{{ route('inventory.adjust.form') }}" class="text-xs text-amber-400 hover:underline">
                            + Record Adjustment
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($recentAdjustments as $adj)
                            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-xs text-amber-400 font-bold">{{ $adj->adjustment_number }}</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded {{ $adj->type === 'addition' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-rose-500/15 text-rose-400' }}">
                                        {{ $adj->type === 'addition' ? '+' : '-' }}{{ number_format($adj->quantity, 1) }}
                                    </span>
                                </div>
                                <div class="text-sm font-semibold text-white">{{ $adj->item->name ?? 'Deleted Item' }}</div>
                                <div class="text-xs text-slate-400 flex items-center justify-between">
                                    <span>Reason: <strong class="text-slate-200 capitalize">{{ str_replace('_', ' ', $adj->reason) }}</strong></span>
                                    <span>{{ $adj->adjustment_date ? $adj->adjustment_date->format('M d, Y') : '' }}</span>
                                </div>
                                @if($adj->notes)
                                    <p class="text-[11px] text-slate-500 italic pt-1 border-t border-white/5">{{ $adj->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

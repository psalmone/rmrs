<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                    <span class="p-2 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    Sales & POS Invoices
                </h2>
                <p class="text-xs text-slate-400 mt-1">Wholesale and retail grain distributions, receipts, and receivables ledger.</p>
            </div>
            <div>
                <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-slate-950 font-bold text-sm shadow-lg shadow-blue-500/20 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New POS Sale
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
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Invoices Issued</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($stats['total_sales']) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Total Billed Revenue</p>
                    <p class="text-2xl font-bold text-emerald-400 mt-1">₱{{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Collected Cash / Bank</p>
                    <p class="text-2xl font-bold text-blue-400 mt-1">₱{{ number_format($stats['total_collected'], 2) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Receivables Balance</p>
                    <p class="text-2xl font-bold text-amber-400 mt-1">₱{{ number_format($stats['outstanding'], 2) }}</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10">
                <form method="GET" action="{{ route('sales.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search Invoice #, Customer..." 
                            class="w-full pl-10 pr-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-blue-400">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="payment_status" onchange="this.form.submit()" class="w-full py-2 px-3 rounded-xl bg-[#0d1c10] border border-white/10 text-slate-200 text-sm focus:outline-none focus:border-blue-400">
                            <option value="">All Payment Statuses</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Fully Paid</option>
                            <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial Balance</option>
                            <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid / On Credit</option>
                        </select>
                    </div>
                    @if($search || $status)
                        <a href="{{ route('sales.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs text-slate-300 font-semibold flex items-center justify-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="rounded-2xl bg-white/[0.02] border border-white/10 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase bg-white/[0.04] text-slate-400 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-4">Invoice #</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4 text-right">Gross Total</th>
                                <th class="px-6 py-4 text-right">Net Billed</th>
                                <th class="px-6 py-4 text-right">Paid</th>
                                <th class="px-6 py-4 text-right">Balance</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($sales as $sale)
                                <tr class="hover:bg-white/[0.02] transition">
                                    <td class="px-6 py-4 font-mono font-bold text-blue-400">
                                        <a href="{{ route('sales.show', $sale) }}" class="hover:underline">
                                            {{ $sale->invoice_number }}
                                        </a>
                                        <p class="text-[11px] text-slate-500 font-sans font-normal">{{ $sale->sale_date ? $sale->sale_date->format('M d, Y') : '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-white">
                                        {{ $sale->customer_name }}
                                        @if($sale->customer)
                                            <p class="text-[11px] text-slate-400">{{ $sale->customer->business_name ?? 'Individual' }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs capitalize font-medium {{ $sale->sale_type === 'wholesale' ? 'bg-purple-500/10 text-purple-300 border border-purple-500/20' : 'bg-slate-500/10 text-slate-300 border border-slate-500/20' }}">
                                            {{ $sale->sale_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-slate-400">
                                        ₱{{ number_format($sale->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-semibold text-white">
                                        ₱{{ number_format($sale->net_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-emerald-400">
                                        ₱{{ number_format($sale->paid_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono {{ $sale->balance_amount > 0 ? 'text-amber-400 font-bold' : 'text-slate-500' }}">
                                        ₱{{ number_format($sale->balance_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($sale->payment_status === 'paid')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Paid</span>
                                        @elseif($sale->payment_status === 'partial')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">Partial</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('sales.show', $sale) }}" class="p-1.5 rounded-lg hover:bg-white/10 text-slate-400 hover:text-white" title="View Receipt">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                        No sales records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sales->hasPages())
                    <div class="px-6 py-4 border-t border-white/10 bg-white/[0.01]">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

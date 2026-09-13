<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('sales.index') }}" class="text-xs text-blue-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                    Invoice: <span class="font-mono text-blue-400">{{ $sale->invoice_number }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Issued {{ $sale->sale_date ? $sale->sale_date->format('M d, Y h:i A') : 'N/A' }} • Cashier: {{ $sale->cashier->name ?? 'System' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Receipt
                </button>
                <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Void this invoice and restore inventory stock?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs font-semibold text-rose-300 hover:bg-rose-500/20">
                        Void Sale
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Invoice Card / Printable Receipt -->
            <div class="p-6 md:p-8 rounded-3xl bg-white/[0.03] border border-white/10 space-y-6 shadow-2xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-white/10 gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Malaptw Rice Mill Delivery & Sales Invoice</span>
                        </div>
                        <div class="text-xl font-bold text-white mt-1">{{ $sale->customer_name }}</div>
                        @if($sale->customer)
                            <div class="text-xs text-slate-400">{{ $sale->customer->business_name ?? 'Individual' }} • {{ $sale->customer->phone ?? 'No Phone' }}</div>
                        @endif
                    </div>
                    <div class="sm:text-right">
                        <span class="text-xs text-slate-400">Payment Status</span>
                        <div class="mt-1">
                            @if($sale->payment_status === 'paid')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PAID</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">PARTIAL (₱{{ number_format($sale->balance_amount, 2) }} DUE)</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">UNPAID (CREDIT)</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.01]">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase bg-white/[0.04] text-slate-400 border-b border-white/10">
                            <tr>
                                <th class="px-5 py-3">Description</th>
                                <th class="px-5 py-3 text-right">Quantity</th>
                                <th class="px-5 py-3 text-right">Unit Price</th>
                                <th class="px-5 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="px-5 py-3">
                                        <div class="font-semibold text-white">{{ $item->item_name }}</div>
                                        <span class="text-[11px] text-slate-500">{{ $item->item->code ?? '' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-medium text-slate-200">
                                        {{ number_format($item->quantity, 2) }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono text-slate-400">
                                        ₱{{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono font-bold text-white">
                                        ₱{{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Financial Totals -->
                <div class="p-5 rounded-2xl bg-[#09150c] border border-white/10 max-w-sm ml-auto space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Gross Total:</span>
                        <span class="font-mono text-white">₱{{ number_format($sale->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Discount:</span>
                        <span class="font-mono text-rose-400">- ₱{{ number_format($sale->discount_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-base border-t border-white/10 pt-2">
                        <span class="text-white">Net Billed:</span>
                        <span class="font-mono text-emerald-400 text-lg">₱{{ number_format($sale->net_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Amount Paid ({{ strtoupper($sale->payment_method) }}):</span>
                        <span class="font-mono text-blue-400 font-semibold">₱{{ number_format($sale->paid_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold border-t border-white/10 pt-2">
                        <span class="text-amber-400">Balance Due:</span>
                        <span class="font-mono text-amber-400 text-lg">₱{{ number_format($sale->balance_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payments Ledger -->
                @if($sale->payments->isNotEmpty())
                    <div class="pt-4 border-t border-white/10 space-y-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Payment Transactions</h4>
                        <div class="space-y-2">
                            @foreach($sale->payments as $pay)
                                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/10 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-mono text-blue-400 font-bold">{{ $pay->reference_number }}</span>
                                        <span class="text-slate-400 ml-2">{{ $pay->payment_date ? $pay->payment_date->format('M d, Y h:i A') : '' }}</span>
                                        <span class="text-slate-500 ml-2">via {{ strtoupper($pay->payment_method) }}</span>
                                    </div>
                                    <div class="font-mono font-bold text-emerald-400 text-sm">
                                        ₱{{ number_format($pay->amount, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($sale->notes)
                    <div class="p-4 rounded-xl bg-white/[0.01] border border-white/5 text-xs text-slate-400">
                        <span class="font-semibold text-slate-300">Notes:</span> {{ $sale->notes }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

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
                    Palay Grain Intakes
                </h2>
                <p class="text-xs text-slate-400 mt-1">Manage receipt of fresh & dry palay sacks, moisture deductions, and farmer payouts.</p>
            </div>
            <div>
                <a href="{{ route('palay-intakes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Intake Ticket
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Alerts -->
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
                    <p class="text-xs text-slate-400 font-medium">Total Intakes</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($stats['total_intakes']) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Total Sacks Received</p>
                    <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($stats['total_sacks']) }} <span class="text-xs font-normal text-slate-400">sacks</span></p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Total Net Weight</p>
                    <p class="text-2xl font-bold text-emerald-400 mt-1">{{ number_format($stats['total_net_kg'], 1) }} <span class="text-xs font-normal text-slate-400">kg</span></p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Total Payouts</p>
                    <p class="text-2xl font-bold text-white mt-1">₱{{ number_format($stats['total_payout'], 2) }}</p>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10">
                <form method="GET" action="{{ route('palay-intakes.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by Ticket #, Farmer Name, Variety..." 
                            class="w-full pl-10 pr-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-amber-400">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="payment_status" onchange="this.form.submit()" class="w-full py-2 px-3 rounded-xl bg-[#0d1c10] border border-white/10 text-slate-200 text-sm focus:outline-none focus:border-amber-400">
                            <option value="">All Payment Statuses</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    @if($search || $status)
                        <a href="{{ route('palay-intakes.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs text-slate-300 font-semibold flex items-center justify-center">
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
                                <th class="px-6 py-4">Ticket</th>
                                <th class="px-6 py-4">Farmer</th>
                                <th class="px-6 py-4">Variety</th>
                                <th class="px-6 py-4 text-right">Sacks</th>
                                <th class="px-6 py-4 text-right">Net Wt. (kg)</th>
                                <th class="px-6 py-4 text-right">Price / kg</th>
                                <th class="px-6 py-4 text-right">Total Amount</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($intakes as $intake)
                                <tr class="hover:bg-white/[0.02] transition">
                                    <td class="px-6 py-4 font-mono font-bold text-amber-400">
                                        <a href="{{ route('palay-intakes.show', $intake) }}" class="hover:underline">
                                            {{ $intake->ticket_number }}
                                        </a>
                                        <p class="text-[11px] text-slate-500 font-sans font-normal">{{ $intake->intake_date ? $intake->intake_date->format('M d, Y') : '' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white">{{ $intake->farmer_name }}</div>
                                        @if($intake->farmer)
                                            <span class="text-[11px] text-slate-500">{{ $intake->farmer->phone }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-400/10 text-amber-300 border border-amber-400/20">
                                            {{ $intake->variety_type }}
                                        </span>
                                        <div class="text-[11px] text-slate-500 mt-0.5">MC: {{ $intake->moisture_content_pct }}%</div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-200">
                                        {{ number_format($intake->bag_count) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-medium text-slate-200">
                                        {{ number_format($intake->net_weight_kg, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-400">
                                        ₱{{ number_format($intake->price_per_kg, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-white">
                                        ₱{{ number_format($intake->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($intake->payment_status === 'paid')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Paid</span>
                                        @elseif($intake->payment_status === 'partial')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">Partial</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('palay-intakes.show', $intake) }}" class="p-1.5 rounded-lg hover:bg-white/10 text-slate-400 hover:text-white" title="View details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('palay-intakes.edit', $intake) }}" class="p-1.5 rounded-lg hover:bg-white/10 text-slate-400 hover:text-amber-400" title="Edit">
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
                                        No intake tickets found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($intakes->hasPages())
                    <div class="px-6 py-4 border-t border-white/10 bg-white/[0.01]">
                        {{ $intakes->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

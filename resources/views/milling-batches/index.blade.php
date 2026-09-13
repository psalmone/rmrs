<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                    <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </span>
                    Milling Production Runs
                </h2>
                <p class="text-xs text-slate-400 mt-1">Monitor hopper input, output yields (milled rice, darak, binlid), and recovery efficiency.</p>
            </div>
            <div>
                <a href="{{ route('milling-batches.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Start Milling Run
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
                    <p class="text-xs text-slate-400 font-medium">Total Batches</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($stats['total_batches']) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">In Progress (Active)</p>
                    <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($stats['in_progress']) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Completed Runs</p>
                    <p class="text-2xl font-bold text-emerald-400 mt-1">{{ number_format($stats['completed']) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10">
                    <p class="text-xs text-slate-400 font-medium">Avg. Recovery Rate</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['avg_recovery'] }}%</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10">
                <form method="GET" action="{{ route('milling-batches.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by Batch #, Variety..." 
                            class="w-full pl-10 pr-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-400">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 rounded-xl bg-[#0d1c10] border border-white/10 text-slate-200 text-sm focus:outline-none focus:border-emerald-400">
                            <option value="">All Statuses</option>
                            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    @if($search || $status)
                        <a href="{{ route('milling-batches.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs text-slate-300 font-semibold flex items-center justify-center">
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
                                <th class="px-6 py-4">Batch Number</th>
                                <th class="px-6 py-4">Variety</th>
                                <th class="px-6 py-4 text-right">Input Sacks</th>
                                <th class="px-6 py-4 text-right">Input Wt. (kg)</th>
                                <th class="px-6 py-4 text-right">Output (kg)</th>
                                <th class="px-6 py-4 text-center">Recovery Rate</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($batches as $batch)
                                <tr class="hover:bg-white/[0.02] transition">
                                    <td class="px-6 py-4 font-mono font-bold text-emerald-400">
                                        <a href="{{ route('milling-batches.show', $batch) }}" class="hover:underline">
                                            {{ $batch->batch_number }}
                                        </a>
                                        <p class="text-[11px] text-slate-500 font-sans font-normal">{{ $batch->created_at->format('M d, Y h:i A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-white">
                                        {{ $batch->palay_variety }}
                                        @if($batch->palayIntake)
                                            <p class="text-[11px] text-amber-400/80 font-mono">{{ $batch->palayIntake->ticket_number }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-200">
                                        {{ number_format($batch->input_sacks) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-medium text-slate-200">
                                        {{ number_format($batch->input_weight_kg, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-semibold text-white">
                                        {{ number_format($batch->total_milled_output_kg, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-mono font-bold {{ $batch->recovery_rate_pct >= 65 ? 'text-emerald-400' : 'text-amber-400' }}">
                                            {{ $batch->recovery_rate_pct }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($batch->status === 'completed')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Completed</span>
                                        @elseif($batch->status === 'in_progress')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">In Progress</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('milling-batches.show', $batch) }}" class="p-1.5 rounded-lg hover:bg-white/10 text-slate-400 hover:text-white" title="View details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('milling-batches.edit', $batch) }}" class="p-1.5 rounded-lg hover:bg-white/10 text-slate-400 hover:text-emerald-400" title="Log Outputs / Complete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                        No milling runs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($batches->hasPages())
                    <div class="px-6 py-4 border-t border-white/10 bg-white/[0.01]">
                        {{ $batches->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

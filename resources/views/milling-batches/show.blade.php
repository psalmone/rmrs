<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('milling-batches.index') }}" class="text-xs text-emerald-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Milling Runs
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                    Batch: <span class="font-mono text-emerald-400">{{ $millingBatch->batch_number }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Started {{ $millingBatch->started_at ? $millingBatch->started_at->format('M d, Y h:i A') : 'N/A' }} • Variety: {{ $millingBatch->palay_variety }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('milling-batches.edit', $millingBatch) }}" class="px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs font-semibold text-emerald-300 hover:bg-emerald-500/20">
                    Log Outputs / Update Run
                </a>
                <form method="POST" action="{{ route('milling-batches.destroy', $millingBatch) }}" onsubmit="return confirm('Are you sure you want to delete this batch record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs font-semibold text-rose-300 hover:bg-rose-500/20">
                        Delete
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

            <!-- Batch Summary Card -->
            <div class="p-6 md:p-8 rounded-3xl bg-white/[0.03] border border-white/10 space-y-6 shadow-2xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-white/10 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Milling Run Profile</span>
                        <div class="text-xl font-bold text-white mt-1">{{ $millingBatch->batch_number }}</div>
                        <div class="text-xs text-slate-400">Operator: {{ $millingBatch->operator->name ?? 'System' }}</div>
                    </div>
                    <div class="sm:text-right">
                        <span class="text-xs text-slate-400">Run Status</span>
                        <div class="mt-1">
                            @if($millingBatch->status === 'completed')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">COMPLETED</span>
                            @elseif($millingBatch->status === 'in_progress')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">IN PROGRESS</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">CANCELLED</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Input vs Output Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Input Raw Sacks</span>
                        <div class="text-lg font-bold text-white mt-1">{{ number_format($millingBatch->input_sacks) }} sacks</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Input Grain Weight</span>
                        <div class="text-lg font-bold text-white mt-1">{{ number_format($millingBatch->input_weight_kg, 2) }} kg</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Total Output Yield</span>
                        <div class="text-lg font-bold text-emerald-400 mt-1">{{ number_format($millingBatch->total_milled_output_kg, 2) }} kg</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Recovery Efficiency</span>
                        <div class="text-2xl font-bold font-mono {{ $millingBatch->recovery_rate_pct >= 65 ? 'text-emerald-400' : 'text-amber-400' }} mt-1">
                            {{ $millingBatch->recovery_rate_pct }}%
                        </div>
                    </div>
                </div>

                <!-- Output Items Breakdown Table -->
                <div class="pt-4 border-t border-white/10 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Logged Outputs & By-Products</h4>
                        <a href="{{ route('milling-batches.edit', $millingBatch) }}" class="text-xs text-emerald-400 hover:underline">
                            + Add / Edit Outputs
                        </a>
                    </div>

                    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.01]">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs uppercase bg-white/[0.04] text-slate-400 border-b border-white/10">
                                <tr>
                                    <th class="px-5 py-3">Category</th>
                                    <th class="px-5 py-3">Item Product</th>
                                    <th class="px-5 py-3 text-right">Quantity / Bags</th>
                                    <th class="px-5 py-3 text-right">Weight (kg)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($millingBatch->outputs as $output)
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-white capitalize">
                                            {{ str_replace('_', ' ', $output->output_type) }}
                                        </td>
                                        <td class="px-5 py-3 text-amber-300">
                                            {{ $output->item->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-5 py-3 text-right font-medium text-slate-200">
                                            {{ number_format($output->quantity_units) }}
                                        </td>
                                        <td class="px-5 py-3 text-right font-mono font-bold text-white">
                                            {{ number_format($output->weight_kg, 2) }} kg
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-slate-500">
                                            No outputs logged yet for this batch. Click <a href="{{ route('milling-batches.edit', $millingBatch) }}" class="text-emerald-400 underline">Log Outputs</a> when milling is finished.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($millingBatch->notes)
                    <div class="p-4 rounded-xl bg-white/[0.01] border border-white/5 text-xs text-slate-400">
                        <span class="font-semibold text-slate-300">Notes:</span> {{ $millingBatch->notes }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

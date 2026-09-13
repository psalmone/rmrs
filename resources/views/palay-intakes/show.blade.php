<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('palay-intakes.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Intakes
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                    Intake Ticket: <span class="font-mono text-amber-400">{{ $palayIntake->ticket_number }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Recorded on {{ $palayIntake->intake_date ? $palayIntake->intake_date->format('M d, Y h:i A') : 'N/A' }} by {{ $palayIntake->receivedBy->name ?? 'System' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Ticket
                </button>
                <a href="{{ route('palay-intakes.edit', $palayIntake) }}" class="px-3.5 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs font-semibold text-amber-300 hover:bg-amber-500/20">
                    Edit Ticket
                </a>
                <form method="POST" action="{{ route('palay-intakes.destroy', $palayIntake) }}" onsubmit="return confirm('Are you sure you want to delete this intake ticket?')">
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

            <!-- Main Ticket Sheet -->
            <div class="p-6 md:p-8 rounded-3xl bg-white/[0.03] border border-white/10 space-y-8 shadow-2xl">
                <!-- Header Info -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-white/10 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-400">Official Weight Ticket</span>
                        <div class="text-xl font-bold text-white mt-1">{{ $palayIntake->farmer_name }}</div>
                        @if($palayIntake->farmer)
                            <div class="text-xs text-slate-400">Registered Farmer • {{ $palayIntake->farmer->phone ?? 'No Phone' }} • {{ $palayIntake->farmer->barangay ?? '' }}</div>
                        @endif
                    </div>
                    <div class="sm:text-right">
                        <span class="text-xs text-slate-400">Payment Status</span>
                        <div class="mt-1">
                            @if($palayIntake->payment_status === 'paid')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PAID</span>
                            @elseif($palayIntake->payment_status === 'partial')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">PARTIAL</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">UNPAID</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Variety Type</span>
                        <div class="text-base font-bold text-amber-400 mt-1">{{ $palayIntake->variety_type }}</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Bag / Sack Count</span>
                        <div class="text-base font-bold text-white mt-1">{{ number_format($palayIntake->bag_count) }} sacks</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Moisture Content</span>
                        <div class="text-base font-bold text-white mt-1">{{ $palayIntake->moisture_content_pct }}%</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-xs text-slate-400">Buying Price / kg</span>
                        <div class="text-base font-bold text-white mt-1">₱{{ number_format($palayIntake->price_per_kg, 2) }}</div>
                    </div>
                </div>

                <!-- Weight Breakdowns -->
                <div class="p-5 rounded-2xl bg-[#09150c] border border-white/10 space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Weighbridge Breakdown</h4>
                    <div class="divide-y divide-white/5 text-sm">
                        <div class="py-2.5 flex justify-between">
                            <span class="text-slate-400">Gross Weight:</span>
                            <span class="font-mono text-white">{{ number_format($palayIntake->gross_weight_kg, 2) }} kg</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-slate-400">Tare Weight (Truck/Bags):</span>
                            <span class="font-mono text-rose-400">- {{ number_format($palayIntake->tare_weight_kg, 2) }} kg</span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-slate-400">Moisture & Impurity Deduction:</span>
                            <span class="font-mono text-rose-400">- {{ number_format($palayIntake->deduction_kg, 2) }} kg</span>
                        </div>
                        <div class="py-3 flex justify-between text-base font-bold border-t border-white/10">
                            <span class="text-emerald-400">Final Net Weight:</span>
                            <span class="font-mono text-emerald-400 text-lg">{{ number_format($palayIntake->net_weight_kg, 2) }} kg</span>
                        </div>
                        <div class="py-3 flex justify-between text-lg font-extrabold border-t border-white/10">
                            <span class="text-white">Total Payout Amount:</span>
                            <span class="font-mono text-amber-400 text-2xl">₱{{ number_format($palayIntake->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Milling Connection -->
                @if($palayIntake->millingBatches->isNotEmpty())
                    <div class="pt-4 border-t border-white/10">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Associated Milling Batches</h4>
                        <div class="space-y-2">
                            @foreach($palayIntake->millingBatches as $batch)
                                <a href="{{ route('milling-batches.show', $batch) }}" class="p-3 rounded-xl bg-white/[0.02] border border-white/10 hover:border-amber-400/40 flex items-center justify-between text-sm block">
                                    <div class="font-mono text-amber-400 font-semibold">{{ $batch->batch_number }}</div>
                                    <div class="text-xs text-slate-400">{{ $batch->input_sacks }} sacks milled • Recovery: {{ $batch->recovery_rate_pct }}%</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($palayIntake->notes)
                    <div class="p-4 rounded-xl bg-white/[0.01] border border-white/5 text-xs text-slate-400">
                        <span class="font-semibold text-slate-300">Notes:</span> {{ $palayIntake->notes }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

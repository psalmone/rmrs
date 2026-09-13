<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('milling-batches.index') }}" class="text-xs text-emerald-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Milling Runs
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white">Start New Milling Batch</h2>
                <p class="text-xs text-slate-400 mt-0.5">Feed raw palay sacks into the mill hopper.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="p-4 mb-6 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300">
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('milling-batches.store') }}" class="space-y-6">
                @csrf

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-400">1. Batch Parameters</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Batch Run Number *</label>
                            <input type="text" name="batch_number" value="{{ old('batch_number', $suggestedBatch) }}" required
                                   class="w-full font-mono text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Start Time *</label>
                            <input type="datetime-local" name="started_at" value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Source Palay Intake (Optional)</label>
                            <select name="palay_intake_id" class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                                <option value="">-- General / Silo Stock --</option>
                                @foreach($recentIntakes as $in)
                                    <option value="{{ $in->id }}" {{ old('palay_intake_id') == $in->id ? 'selected' : '' }}>
                                        {{ $in->ticket_number }} ({{ $in->farmer_name }} - {{ $in->variety_type }}, {{ $in->bag_count }} sacks)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Palay Variety *</label>
                            <input type="text" name="palay_variety" value="{{ old('palay_variety', 'Inbred') }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-400">2. Hopper Input</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Input Sacks / Bags *</label>
                            <input type="number" name="input_sacks" value="{{ old('input_sacks', 20) }}" min="1" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                            <span class="text-[11px] text-slate-500">Will be deducted from raw grain inventory.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Total Input Weight (kg) *</label>
                            <input type="number" step="0.01" name="input_weight_kg" value="{{ old('input_weight_kg', 1000) }}" min="1" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Run Notes & Machine Setting</label>
                    <textarea name="notes" rows="3" placeholder="Machine setting, moisture at feeding, huller pressure..."
                              class="w-full text-sm p-3 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('milling-batches.index') }}" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-sm font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20">
                        Start Milling Batch
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

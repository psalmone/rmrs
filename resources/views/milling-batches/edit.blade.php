<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('milling-batches.show', $millingBatch) }}" class="text-xs text-emerald-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Batch
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white">Log Outputs: <span class="font-mono text-emerald-400">{{ $millingBatch->batch_number }}</span></h2>
                <p class="text-xs text-slate-400 mt-0.5">Record milled white rice yield, bran (darak), broken rice (binlid), and husks (ipa).</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="p-4 mb-6 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300">
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('milling-batches.update', $millingBatch) }}" 
                  x-data="{
                      inputKg: {{ $millingBatch->input_weight_kg }},
                      milledKg: 0,
                      darakKg: 0,
                      binlidKg: 0,
                      ipaKg: 0,
                      get totalOutputKg() {
                          return (parseFloat(this.milledKg) || 0) + 
                                 (parseFloat(this.darakKg) || 0) + 
                                 (parseFloat(this.binlidKg) || 0) + 
                                 (parseFloat(this.ipaKg) || 0);
                      },
                      get recoveryRate() {
                          if (this.inputKg <= 0) return '0.0';
                          return ((this.totalOutputKg / this.inputKg) * 100).toFixed(1);
                      }
                  }"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Batch Status & Timing -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-400">1. Status & Timing</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Batch Run Status *</label>
                            <select name="status" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                                <option value="in_progress" {{ old('status', $millingBatch->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $millingBatch->status) === 'completed' ? 'selected' : '' }}>Completed (Commit Outputs to Stock)</option>
                                <option value="cancelled" {{ old('status', $millingBatch->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Completed Time</label>
                            <input type="datetime-local" name="completed_at" value="{{ old('completed_at', $millingBatch->completed_at ? $millingBatch->completed_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">
                        </div>
                    </div>
                </div>

                <!-- Milling Output Grid -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-400">2. Output Yields</h3>
                        <span class="text-xs text-slate-400 font-mono">Hopper Input: {{ number_format($millingBatch->input_weight_kg, 2) }} kg</span>
                    </div>

                    <!-- Milled Rice Output -->
                    <div class="p-4 rounded-xl bg-white/[0.02] border border-white/10 space-y-3">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">A. Head Milled Rice (White Rice)</span>
                        <input type="hidden" name="outputs[0][output_type]" value="milled_rice">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Select Inventory Item</label>
                                <select name="outputs[0][item_id]" class="w-full text-xs px-3 py-2 rounded-xl bg-[#0d1c10] border border-white/10 text-white">
                                    @foreach($inventoryItems->where('category', 'milled_rice') as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Quantity (Sacks/Bags)</label>
                                <input type="number" step="1" name="outputs[0][quantity_units]" value="12" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Weight (kg)</label>
                                <input type="number" step="0.01" name="outputs[0][weight_kg]" x-model="milledKg" placeholder="600.00" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- By-Products: Darak (Bran) -->
                    <div class="p-4 rounded-xl bg-white/[0.02] border border-white/10 space-y-3">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">B. Rice Bran (Darak)</span>
                        <input type="hidden" name="outputs[1][output_type]" value="darak_bran">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Select Inventory Item</label>
                                <select name="outputs[1][item_id]" class="w-full text-xs px-3 py-2 rounded-xl bg-[#0d1c10] border border-white/10 text-white">
                                    @foreach($inventoryItems->where('category', 'by_product') as $item)
                                        <option value="{{ $item->id }}" {{ str_contains(strtolower($item->name), 'darak') ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Quantity (Sacks/Bags)</label>
                                <input type="number" step="1" name="outputs[1][quantity_units]" value="2" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Weight (kg)</label>
                                <input type="number" step="0.01" name="outputs[1][weight_kg]" x-model="darakKg" placeholder="80.00" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- By-Products: Binlid (Broken Rice) -->
                    <div class="p-4 rounded-xl bg-white/[0.02] border border-white/10 space-y-3">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">C. Broken Rice (Binlid)</span>
                        <input type="hidden" name="outputs[2][output_type]" value="binlid_broken">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Select Inventory Item</label>
                                <select name="outputs[2][item_id]" class="w-full text-xs px-3 py-2 rounded-xl bg-[#0d1c10] border border-white/10 text-white">
                                    @foreach($inventoryItems->where('category', 'by_product') as $item)
                                        <option value="{{ $item->id }}" {{ str_contains(strtolower($item->name), 'binlid') ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Quantity (Sacks/Bags)</label>
                                <input type="number" step="1" name="outputs[2][quantity_units]" value="1" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Weight (kg)</label>
                                <input type="number" step="0.01" name="outputs[2][weight_kg]" x-model="binlidKg" placeholder="40.00" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Efficiency Callout -->
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-emerald-300 font-medium">Accumulated Output Weight:</span>
                            <div class="text-2xl font-bold font-mono text-white">
                                <span x-text="totalOutputKg.toFixed(2)">0.00</span> kg
                            </div>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs text-emerald-300 font-medium">Estimated Milling Recovery Rate:</span>
                            <div class="text-3xl font-bold text-emerald-400 font-mono">
                                <span x-text="recoveryRate">0.0</span>%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Run Notes</label>
                    <textarea name="notes" rows="3" class="w-full text-sm p-3 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-emerald-400">{{ old('notes', $millingBatch->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('milling-batches.show', $millingBatch) }}" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-sm font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20">
                        Save Output & Update Batch
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

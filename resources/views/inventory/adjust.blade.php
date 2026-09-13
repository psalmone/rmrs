<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('inventory.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Inventory
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white">Manual Stock Adjustment</h2>
                <p class="text-xs text-slate-400 mt-0.5">Log spillage, ruptured sacks, silo moisture shrinkage, or physical audit counting differences.</p>
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

            <form method="POST" action="{{ route('inventory.adjust.process') }}" 
                  x-data="{
                      type: 'subtraction',
                      quantity: 1,
                      selectedId: '{{ old('item_id', $selectedItemId ?? $items->first()->id ?? '') }}',
                      items: {{ json_encode($items) }},

                      get currentStock() {
                          let found = this.items.find(i => i.id == this.selectedId);
                          return found ? parseFloat(found.current_stock) : 0;
                      },
                      get itemUnit() {
                          let found = this.items.find(i => i.id == this.selectedId);
                          return found ? found.unit : '';
                      },
                      get resultingStock() {
                          let qty = parseFloat(this.quantity) || 0;
                          if (this.type === 'addition') {
                              return (this.currentStock + qty).toFixed(2);
                          } else {
                              return Math.max(0, this.currentStock - qty).toFixed(2);
                          }
                      }
                  }"
                  class="space-y-6">
                @csrf

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">1. Adjustment Header</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Adjustment Reference # *</label>
                            <input type="text" name="adjustment_number" value="{{ old('adjustment_number', $suggestedAdjustment) }}" required
                                   class="w-full font-mono text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Date & Time *</label>
                            <input type="datetime-local" name="adjustment_date" value="{{ old('adjustment_date', now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">2. Item & Quantity</h3>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Select Item to Adjust *</label>
                        <select name="item_id" x-model="selectedId" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                            @foreach($items as $prod)
                                <option value="{{ $prod->id }}">
                                    [{{ $prod->code }}] {{ $prod->name }} — Current: {{ number_format($prod->current_stock) }} {{ $prod->unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Adjustment Action *</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="p-3 rounded-xl border flex items-center justify-center gap-2 cursor-pointer transition text-xs font-bold"
                                       :class="type === 'subtraction' ? 'bg-rose-500/20 border-rose-500/40 text-rose-300' : 'bg-white/5 border-white/10 text-slate-400'">
                                    <input type="radio" name="type" value="subtraction" x-model="type" class="hidden">
                                    <span>- Deduct Stock</span>
                                </label>
                                <label class="p-3 rounded-xl border flex items-center justify-center gap-2 cursor-pointer transition text-xs font-bold"
                                       :class="type === 'addition' ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-300' : 'bg-white/5 border-white/10 text-slate-400'">
                                    <input type="radio" name="type" value="addition" x-model="type" class="hidden">
                                    <span>+ Add Stock</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Quantity Units *</label>
                            <input type="number" step="0.01" min="0.01" name="quantity" x-model="quantity" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Reason for Adjustment *</label>
                        <select name="reason" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                            <option value="spillage">Spillage / Dropped Sack on Warehouse Floor</option>
                            <option value="bag_damage">Bag Damage (Torn Sack / Water / Rodent Damage)</option>
                            <option value="silo_shrinkage">Silo Shrinkage (Moisture loss / Evaporation)</option>
                            <option value="physical_audit">Physical Audit Difference (Periodic Warehouse Inventory Count)</option>
                            <option value="return_from_buyer">Return from Buyer (Undamaged / Restocked)</option>
                            <option value="initial_stock">Initial Baseline Inventory Setup</option>
                            <option value="other">Other Reason</option>
                        </select>
                    </div>

                    <!-- Live Math Preview Callout -->
                    <div class="p-4 rounded-xl bg-[#09150c] border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400">Current Stock:</span>
                            <div class="text-lg font-mono font-bold text-white">
                                <span x-text="currentStock.toFixed(2)">0.00</span> <span class="text-xs font-normal text-slate-400" x-text="itemUnit"></span>
                            </div>
                        </div>
                        <div class="text-center font-mono font-bold text-lg" :class="type === 'addition' ? 'text-emerald-400' : 'text-rose-400'">
                            <span x-text="type === 'addition' ? '+' : '-'"></span><span x-text="(parseFloat(quantity) || 0).toFixed(2)"></span>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs text-amber-300 font-semibold">Resulting New Stock:</span>
                            <div class="text-2xl font-mono font-bold text-amber-400">
                                <span x-text="resultingStock">0.00</span> <span class="text-xs font-normal text-slate-400" x-text="itemUnit"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Auditor / Operator Remarks</label>
                    <textarea name="notes" rows="3" placeholder="Specify warehouse location, aisle number, supervisor approval..."
                              class="w-full text-sm p-3 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-sm font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20">
                        Commit Stock Adjustment
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

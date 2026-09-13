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
                <h2 class="text-2xl font-bold tracking-tight text-white">Edit Item: <span class="font-mono text-amber-400">{{ $item->code }}</span></h2>
                <p class="text-xs text-slate-400 mt-0.5">Update prices, reorder thresholds, and descriptions.</p>
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

            <form method="POST" action="{{ route('inventory.update', $item) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">1. Product Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Item Code / SKU *</label>
                            <input type="text" name="code" value="{{ old('code', $item->code) }}" required
                                   class="w-full font-mono text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Category *</label>
                            <select name="category" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                                <option value="milled_rice" {{ old('category', $item->category) === 'milled_rice' ? 'selected' : '' }}>Milled White Rice</option>
                                <option value="raw_palay" {{ old('category', $item->category) === 'raw_palay' ? 'selected' : '' }}>Raw Palay (Paddy Grain)</option>
                                <option value="by_product" {{ old('category', $item->category) === 'by_product' ? 'selected' : '' }}>By-Product (Bran/Darak/Binlid/Ipa)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Product Display Name *</label>
                            <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Grain Variety</label>
                            <input type="text" name="variety" value="{{ old('variety', $item->variety) }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Unit of Measurement *</label>
                        <select name="unit" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                            <option value="sack_50kg" {{ old('unit', $item->unit) === 'sack_50kg' ? 'selected' : '' }}>Sack (50 kg)</option>
                            <option value="sack_25kg" {{ old('unit', $item->unit) === 'sack_25kg' ? 'selected' : '' }}>Sack (25 kg)</option>
                            <option value="sack_10kg" {{ old('unit', $item->unit) === 'sack_10kg' ? 'selected' : '' }}>Sack (10 kg)</option>
                            <option value="sack_5kg" {{ old('unit', $item->unit) === 'sack_5kg' ? 'selected' : '' }}>Sack (5 kg)</option>
                            <option value="kg" {{ old('unit', $item->unit) === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                            <option value="sack" {{ old('unit', $item->unit) === 'sack' ? 'selected' : '' }}>Sack (Generic)</option>
                        </select>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">2. Thresholds & Pricing</h3>

                    <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Current Stock on Hand:</span>
                        <span class="text-lg font-mono font-bold text-white">{{ number_format($item->current_stock, 1) }} {{ $item->unit }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 italic">To adjust the physical count due to damage, shrinkage, or counting audits, use the <a href="{{ route('inventory.adjust.form', ['item_id' => $item->id]) }}" class="text-amber-400 underline">Stock Adjustment Form</a>.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Reorder Level *</label>
                            <input type="number" step="0.01" min="0" name="reorder_level" value="{{ old('reorder_level', $item->reorder_level) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Unit Cost (₱) *</label>
                            <input type="number" step="0.01" min="0" name="unit_cost" value="{{ old('unit_cost', $item->unit_cost) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Unit Price (₱) *</label>
                            <input type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded bg-white/5 border-white/20 text-amber-500 focus:ring-0">
                            <span class="text-xs font-semibold text-slate-300">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="if(confirm('Delete this product permanently?')) document.getElementById('delete-item-form').submit();" class="text-xs text-rose-400 hover:underline font-semibold">
                        Delete Product
                    </button>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-sm font-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20">
                            Update Item
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-item-form" method="POST" action="{{ route('inventory.destroy', $item) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>
</x-app-layout>

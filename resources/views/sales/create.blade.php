<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('sales.index') }}" class="text-xs text-blue-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white">Create Sale / POS Terminal</h2>
                <p class="text-xs text-slate-400 mt-0.5">Bill rice bags, darak, and binlid to walk-in or credit customers.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="p-4 mb-6 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300">
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" 
                  x-data="{
                      items: [
                          { item_id: '{{ $items->first()->id ?? '' }}', quantity: 1, unit_price: {{ $items->first()->unit_price ?? 0 }} }
                      ],
                      discount: 0,
                      paidAmount: 0,
                      availableItems: {{ json_encode($items) }},

                      addItem() {
                          let defaultItem = this.availableItems[0] || {};
                          this.items.push({
                              item_id: defaultItem.id || '',
                              quantity: 1,
                              unit_price: defaultItem.unit_price || 0
                          });
                      },
                      removeItem(index) {
                          if (this.items.length > 1) {
                              this.items.splice(index, 1);
                          }
                      },
                      onItemChange(index) {
                          let selectedId = this.items[index].item_id;
                          let found = this.availableItems.find(i => i.id == selectedId);
                          if (found) {
                              this.items[index].unit_price = found.unit_price;
                          }
                      },
                      get subtotal() {
                          return this.items.reduce((sum, line) => {
                              return sum + ((parseFloat(line.quantity) || 0) * (parseFloat(line.unit_price) || 0));
                          }, 0);
                      },
                      get netTotal() {
                          return Math.max(0, this.subtotal - (parseFloat(this.discount) || 0));
                      },
                      get balance() {
                          return Math.max(0, this.netTotal - (parseFloat(this.paidAmount) || 0));
                      }
                  }"
                  class="space-y-6">
                @csrf

                <!-- Sale Header / Invoice Info -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-blue-400">1. Invoice Header</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Invoice Number *</label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number', $suggestedInvoice) }}" required
                                   class="w-full font-mono text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-blue-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Sale Date *</label>
                            <input type="datetime-local" name="sale_date" value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-blue-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Sale Type *</label>
                            <select name="sale_type" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-blue-400">
                                <option value="retail">Retail (Per sack/bag)</option>
                                <option value="wholesale">Wholesale (Bulk / Distributor)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Registered Customer (Wholesale/Credit)</label>
                            <select name="customer_id" class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-blue-400">
                                <option value="">-- Choose Customer or leave for Walk-in --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} {{ $c->business_name ? "({$c->business_name})" : '' }} • Bal: ₱{{ number_format($c->outstanding_balance, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Customer Name / Walk-in *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', 'Walk-in Customer') }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-blue-400">
                        </div>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-400">2. Products & Inventory Items</h3>
                        <button type="button" @click="addItem()" class="px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-xs font-semibold text-blue-300 hover:bg-blue-500/20 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Line Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(line, index) in items" :key="index">
                            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/10 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                <div class="sm:col-span-5">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Product</label>
                                    <select :name="'items[' + index + '][item_id]'" x-model="line.item_id" @change="onItemChange(index)" class="w-full text-xs px-3 py-2 rounded-xl bg-[#0d1c10] border border-white/10 text-white">
                                        @foreach($items as $prod)
                                            <option value="{{ $prod->id }}">
                                                {{ $prod->name }} [Stock: {{ number_format($prod->current_stock) }}] - ₱{{ number_format($prod->unit_price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Quantity</label>
                                    <input type="number" step="0.01" min="0.01" :name="'items[' + index + '][quantity]'" x-model="line.quantity" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Unit Price (₱)</label>
                                    <input type="number" step="0.01" min="0" :name="'items[' + index + '][unit_price]'" x-model="line.unit_price" class="w-full text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white">
                                </div>

                                <div class="sm:col-span-2 text-right">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Subtotal</label>
                                    <div class="text-sm font-mono font-bold text-white pt-1">
                                        ₱<span x-text="((parseFloat(line.quantity) || 0) * (parseFloat(line.unit_price) || 0)).toFixed(2)"></span>
                                    </div>
                                </div>

                                <div class="sm:col-span-1 text-right sm:pt-4">
                                    <button type="button" @click="removeItem(index)" class="p-2 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-rose-500/10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Settlement & Payment Summary -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-blue-400">3. Settlement & Payment</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Discount Amount (₱)</label>
                            <input type="number" step="0.01" min="0" name="discount_amount" x-model="discount"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-blue-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Method *</label>
                            <select name="payment_method" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-blue-400">
                                <option value="cash">Cash Tender</option>
                                <option value="gcash">GCash E-Wallet</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit">Customer Credit / Charge</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Amount Paid (₱) *</label>
                            <input type="number" step="0.01" min="0" name="paid_amount" x-model="paidAmount" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-blue-400">
                        </div>
                    </div>

                    <!-- Live Math Settlement Callout -->
                    <div class="mt-4 p-5 rounded-xl bg-[#09150c] border border-white/10 divide-y divide-white/10 text-sm">
                        <div class="py-2 flex justify-between">
                            <span class="text-slate-400">Gross Subtotal:</span>
                            <span class="font-mono text-white">₱<span x-text="subtotal.toFixed(2)">0.00</span></span>
                        </div>
                        <div class="py-2 flex justify-between">
                            <span class="text-slate-400">Discount:</span>
                            <span class="font-mono text-rose-400">- ₱<span x-text="(parseFloat(discount) || 0).toFixed(2)">0.00</span></span>
                        </div>
                        <div class="py-2.5 flex justify-between font-bold text-base">
                            <span class="text-white">Net Total Payable:</span>
                            <span class="font-mono text-emerald-400 text-xl">₱<span x-text="netTotal.toFixed(2)">0.00</span></span>
                        </div>
                        <div class="py-2.5 flex justify-between">
                            <span class="text-slate-400">Amount Tendered/Paid:</span>
                            <span class="font-mono text-blue-400 font-semibold">₱<span x-text="(parseFloat(paidAmount) || 0).toFixed(2)">0.00</span></span>
                        </div>
                        <div class="py-3 flex justify-between text-base font-bold">
                            <span class="text-amber-400">Remaining Balance Due:</span>
                            <span class="font-mono text-amber-400 text-xl">₱<span x-text="balance.toFixed(2)">0.00</span></span>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Transaction Notes / Delivery Details</label>
                    <textarea name="notes" rows="2" placeholder="Driver name, vehicle plate, delivery instructions..."
                              class="w-full text-sm p-3 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-blue-400">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('sales.index') }}" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-sm font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-slate-950 font-bold text-sm shadow-lg shadow-blue-500/20">
                        Complete Sale & Deduct Stock
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('palay-intakes.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Intakes
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white">Record Palay Intake</h2>
                <p class="text-xs text-slate-400 mt-0.5">Weighing ticket and farmer settlement calculator.</p>
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

            <form method="POST" action="{{ route('palay-intakes.store') }}" 
                  x-data="{
                      gross: 0,
                      tare: 0,
                      deduction: 0,
                      price: 21.50,
                      get netWeight() {
                          let net = (parseFloat(this.gross) || 0) - (parseFloat(this.tare) || 0) - (parseFloat(this.deduction) || 0);
                          return Math.max(0, net).toFixed(2);
                      },
                      get totalPayout() {
                          let payout = (parseFloat(this.netWeight) || 0) * (parseFloat(this.price) || 0);
                          return payout.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                      }
                  }"
                  class="space-y-6">
                @csrf

                <!-- Basic Identification Card -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">1. Intake Identification</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Ticket Number *</label>
                            <input type="text" name="ticket_number" value="{{ old('ticket_number', $suggestedTicket) }}" required
                                   class="w-full font-mono text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Intake Date & Time *</label>
                            <input type="datetime-local" name="intake_date" value="{{ old('intake_date', now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Palay Variety *</label>
                            <select name="variety_type" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                                <option value="Inbred" {{ old('variety_type') === 'Inbred' ? 'selected' : '' }}>Inbred (Regular)</option>
                                <option value="Hybrid" {{ old('variety_type') === 'Hybrid' ? 'selected' : '' }}>Hybrid (SL-8H / High Yield)</option>
                                <option value="Fresh Wet" {{ old('variety_type') === 'Fresh Wet' ? 'selected' : '' }}>Fresh Wet (Basa)</option>
                                <option value="Dry" {{ old('variety_type') === 'Dry' ? 'selected' : '' }}>Dry (Tuyo / Clean)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Registered Farmer (Optional)</label>
                            <select name="farmer_id" class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                                <option value="">-- Choose Registered Farmer --</option>
                                @foreach($farmers as $f)
                                    <option value="{{ $f->id }}" {{ old('farmer_id') == $f->id ? 'selected' : '' }}>{{ $f->name }} ({{ $f->phone ?? 'No Phone' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Farmer / Supplier Name *</label>
                            <input type="text" name="farmer_name" value="{{ old('farmer_name') }}" placeholder="Enter full name if not registered" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>
                </div>

                <!-- Weight & Moisture Computation Card -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">2. Weight & Moisture Analysis</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Sack Count (Bags) *</label>
                            <input type="number" name="bag_count" value="{{ old('bag_count', 50) }}" min="1" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Gross Weight (kg) *</label>
                            <input type="number" step="0.01" name="gross_weight_kg" x-model="gross" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Tare Weight (kg) *</label>
                            <input type="number" step="0.01" name="tare_weight_kg" x-model="tare" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Moisture Content (%) *</label>
                            <input type="number" step="0.1" name="moisture_content_pct" value="{{ old('moisture_content_pct', 14.0) }}" min="5" max="35" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Deduction (kg)</label>
                            <input type="number" step="0.01" name="deduction_kg" x-model="deduction" placeholder="Foreign matter / moisture"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Buying Price / kg (₱) *</label>
                            <input type="number" step="0.01" name="price_per_kg" x-model="price" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Status *</label>
                            <select name="payment_status" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                                <option value="unpaid">Unpaid (Pay Later)</option>
                                <option value="partial">Partial</option>
                                <option value="paid" selected>Paid (Cash on Delivery)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Live Math Display Callout -->
                    <div class="mt-4 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-amber-300 font-medium">Calculated Net Weight:</span>
                            <div class="text-2xl font-bold font-mono text-white">
                                <span x-text="netWeight">0.00</span> <span class="text-sm font-normal text-slate-400">kg</span>
                            </div>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs text-amber-300 font-medium">Total Payout Amount:</span>
                            <div class="text-3xl font-bold text-amber-400 font-mono">
                                ₱<span x-text="totalPayout">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Operator Notes / Observations</label>
                    <textarea name="notes" rows="3" placeholder="Condition of sacks, truck license plate, moisture notes..."
                              class="w-full text-sm p-3 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('palay-intakes.index') }}" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-sm font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20">
                        Record Palay Intake
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

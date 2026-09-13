<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('palay-intakes.show', $palayIntake) }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-2 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Ticket
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-white">Edit Intake: <span class="font-mono text-amber-400">{{ $palayIntake->ticket_number }}</span></h2>
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

            <form method="POST" action="{{ route('palay-intakes.update', $palayIntake) }}" 
                  x-data="{
                      gross: {{ $palayIntake->gross_weight_kg }},
                      tare: {{ $palayIntake->tare_weight_kg }},
                      deduction: {{ $palayIntake->deduction_kg }},
                      price: {{ $palayIntake->price_per_kg }},
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
                @method('PUT')

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">1. Intake Identification</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Ticket Number *</label>
                            <input type="text" name="ticket_number" value="{{ old('ticket_number', $palayIntake->ticket_number) }}" required
                                   class="w-full font-mono text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Intake Date & Time *</label>
                            <input type="datetime-local" name="intake_date" value="{{ old('intake_date', $palayIntake->intake_date ? $palayIntake->intake_date->format('Y-m-d\TH:i') : '') }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Palay Variety *</label>
                            <select name="variety_type" required class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                                <option value="Inbred" {{ old('variety_type', $palayIntake->variety_type) === 'Inbred' ? 'selected' : '' }}>Inbred (Regular)</option>
                                <option value="Hybrid" {{ old('variety_type', $palayIntake->variety_type) === 'Hybrid' ? 'selected' : '' }}>Hybrid (SL-8H / High Yield)</option>
                                <option value="Fresh Wet" {{ old('variety_type', $palayIntake->variety_type) === 'Fresh Wet' ? 'selected' : '' }}>Fresh Wet (Basa)</option>
                                <option value="Dry" {{ old('variety_type', $palayIntake->variety_type) === 'Dry' ? 'selected' : '' }}>Dry (Tuyo / Clean)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Registered Farmer</label>
                            <select name="farmer_id" class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-[#0d1c10] border border-white/10 text-white focus:outline-none focus:border-amber-400">
                                <option value="">-- Choose Registered Farmer --</option>
                                @foreach($farmers as $f)
                                    <option value="{{ $f->id }}" {{ old('farmer_id', $palayIntake->farmer_id) == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Farmer / Supplier Name *</label>
                            <input type="text" name="farmer_name" value="{{ old('farmer_name', $palayIntake->farmer_name) }}" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400">2. Weight & Moisture Analysis</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Sack Count *</label>
                            <input type="number" name="bag_count" value="{{ old('bag_count', $palayIntake->bag_count) }}" min="1" required
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
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Moisture (%) *</label>
                            <input type="number" step="0.1" name="moisture_content_pct" value="{{ old('moisture_content_pct', $palayIntake->moisture_content_pct) }}" min="5" max="35" required
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Deduction (kg)</label>
                            <input type="number" step="0.01" name="deduction_kg" x-model="deduction"
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
                                <option value="unpaid" {{ old('payment_status', $palayIntake->payment_status) === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ old('payment_status', $palayIntake->payment_status) === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ old('payment_status', $palayIntake->payment_status) === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-amber-300 font-medium">Recalculated Net Weight:</span>
                            <div class="text-2xl font-bold font-mono text-white">
                                <span x-text="netWeight">0.00</span> kg
                            </div>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs text-amber-300 font-medium">Total Payout:</span>
                            <div class="text-3xl font-bold text-amber-400 font-mono">
                                ₱<span x-text="totalPayout">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Notes</label>
                    <textarea name="notes" rows="3" class="w-full text-sm p-3 rounded-xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-amber-400">{{ old('notes', $palayIntake->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('palay-intakes.show', $palayIntake) }}" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-sm font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20">
                        Update Ticket
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

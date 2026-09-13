<?php

namespace App\Http\Controllers;

use App\Models\PalayIntake;
use App\Models\Farmer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PalayIntakeController extends Controller
{
    /**
     * Display a listing of palay intakes.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('payment_status');

        $query = PalayIntake::with(['farmer', 'receivedBy'])->latest('intake_date');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('farmer_name', 'like', "%{$search}%")
                  ->orWhere('variety_type', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['paid', 'partial', 'unpaid'])) {
            $query->where('payment_status', $status);
        }

        $intakes = $query->paginate(15)->withQueryString();

        // Summary counters
        $stats = [
            'total_intakes' => PalayIntake::count(),
            'total_sacks' => PalayIntake::sum('bag_count'),
            'total_net_kg' => PalayIntake::sum('net_weight_kg'),
            'total_payout' => PalayIntake::sum('total_amount'),
        ];

        return view('palay-intakes.index', compact('intakes', 'stats', 'search', 'status'));
    }

    /**
     * Show the form for creating a new intake.
     */
    public function create()
    {
        $farmers = Farmer::orderBy('name')->get();
        // Generate next suggested ticket number: e.g. TKT-2026-0001
        $count = PalayIntake::whereYear('created_at', now()->year)->count() + 1;
        $suggestedTicket = 'TKT-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('palay-intakes.create', compact('farmers', 'suggestedTicket'));
    }

    /**
     * Store a newly created intake in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|max:50|unique:palay_intakes,ticket_number',
            'farmer_id' => 'nullable|exists:farmers,id',
            'farmer_name' => 'required_without:farmer_id|nullable|string|max:255',
            'variety_type' => 'required|string|max:100',
            'bag_count' => 'required|integer|min:1',
            'gross_weight_kg' => 'required|numeric|min:0.01',
            'tare_weight_kg' => 'required|numeric|min:0',
            'moisture_content_pct' => 'required|numeric|min:0|max:100',
            'deduction_kg' => 'nullable|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'payment_status' => 'required|in:unpaid,partial,paid',
            'intake_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Resolve farmer name if ID selected
        if (!empty($validated['farmer_id'])) {
            $farmer = Farmer::find($validated['farmer_id']);
            $validated['farmer_name'] = $farmer ? $farmer->name : ($validated['farmer_name'] ?? 'Unknown');
        }

        // Calculate Net Weight: Gross - Tare - Deductions
        $gross = (float) $validated['gross_weight_kg'];
        $tare = (float) $validated['tare_weight_kg'];
        $deduction = (float) ($validated['deduction_kg'] ?? 0);
        $netWeight = max(0, $gross - $tare - $deduction);
        $totalAmount = $netWeight * (float) $validated['price_per_kg'];

        $validated['net_weight_kg'] = $netWeight;
        $validated['total_amount'] = $totalAmount;
        $validated['received_by_user_id'] = Auth::id();

        DB::transaction(function () use ($validated) {
            PalayIntake::create($validated);

            // Automatically increase Raw Palay stock in Inventory if item found
            $palayItem = Item::where('category', 'raw_palay')
                ->where('variety', $validated['variety_type'])
                ->first();

            if (!$palayItem) {
                // fallback to any raw_palay item
                $palayItem = Item::where('category', 'raw_palay')->first();
            }

            if ($palayItem) {
                $palayItem->increment('current_stock', $validated['bag_count']);
            }
        });

        return redirect()->route('palay-intakes.index')
            ->with('success', "Palay Intake ticket #{$validated['ticket_number']} recorded successfully.");
    }

    /**
     * Display the specified intake.
     */
    public function show(PalayIntake $palayIntake)
    {
        $palayIntake->load(['farmer', 'receivedBy', 'millingBatches']);
        return view('palay-intakes.show', compact('palayIntake'));
    }

    /**
     * Show the form for editing the specified intake.
     */
    public function edit(PalayIntake $palayIntake)
    {
        $farmers = Farmer::orderBy('name')->get();
        return view('palay-intakes.edit', compact('palayIntake', 'farmers'));
    }

    /**
     * Update the specified intake in storage.
     */
    public function update(Request $request, PalayIntake $palayIntake)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|max:50|unique:palay_intakes,ticket_number,' . $palayIntake->id,
            'farmer_id' => 'nullable|exists:farmers,id',
            'farmer_name' => 'required_without:farmer_id|nullable|string|max:255',
            'variety_type' => 'required|string|max:100',
            'bag_count' => 'required|integer|min:1',
            'gross_weight_kg' => 'required|numeric|min:0.01',
            'tare_weight_kg' => 'required|numeric|min:0',
            'moisture_content_pct' => 'required|numeric|min:0|max:100',
            'deduction_kg' => 'nullable|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'payment_status' => 'required|in:unpaid,partial,paid',
            'intake_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if (!empty($validated['farmer_id'])) {
            $farmer = Farmer::find($validated['farmer_id']);
            $validated['farmer_name'] = $farmer ? $farmer->name : ($validated['farmer_name'] ?? 'Unknown');
        }

        $gross = (float) $validated['gross_weight_kg'];
        $tare = (float) $validated['tare_weight_kg'];
        $deduction = (float) ($validated['deduction_kg'] ?? 0);
        $netWeight = max(0, $gross - $tare - $deduction);
        $totalAmount = $netWeight * (float) $validated['price_per_kg'];

        $validated['net_weight_kg'] = $netWeight;
        $validated['total_amount'] = $totalAmount;

        $palayIntake->update($validated);

        return redirect()->route('palay-intakes.show', $palayIntake)
            ->with('success', "Intake ticket #{$palayIntake->ticket_number} updated successfully.");
    }

    /**
     * Remove the specified intake from storage.
     */
    public function destroy(PalayIntake $palayIntake)
    {
        $ticket = $palayIntake->ticket_number;
        $palayIntake->delete();

        return redirect()->route('palay-intakes.index')
            ->with('success', "Palay Intake ticket #{$ticket} deleted successfully.");
    }
}

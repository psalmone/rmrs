<?php

namespace App\Http\Controllers;

use App\Models\MillingBatch;
use App\Models\MillingOutput;
use App\Models\PalayIntake;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MillingBatchController extends Controller
{
    /**
     * Display a listing of milling runs.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = MillingBatch::with(['operator', 'palayIntake', 'outputs.item'])->latest();

        if ($status && in_array($status, ['in_progress', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhere('palay_variety', 'like', "%{$search}%");
            });
        }

        $batches = $query->paginate(15)->withQueryString();

        $stats = [
            'total_batches' => MillingBatch::count(),
            'in_progress' => MillingBatch::where('status', 'in_progress')->count(),
            'completed' => MillingBatch::where('status', 'completed')->count(),
            'avg_recovery' => round(MillingBatch::where('status', 'completed')->avg('recovery_rate_pct') ?? 0, 1),
        ];

        return view('milling-batches.index', compact('batches', 'stats', 'status', 'search'));
    }

    /**
     * Show the form for creating a new milling batch.
     */
    public function create()
    {
        // Available intakes for milling reference
        $recentIntakes = PalayIntake::latest('intake_date')->take(20)->get();

        $count = MillingBatch::whereYear('created_at', now()->year)->count() + 1;
        $suggestedBatch = 'BATCH-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        return view('milling-batches.create', compact('recentIntakes', 'suggestedBatch'));
    }

    /**
     * Store a newly created milling run.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_number' => 'required|string|max:50|unique:milling_batches,batch_number',
            'palay_intake_id' => 'nullable|exists:palay_intakes,id',
            'palay_variety' => 'required|string|max:100',
            'input_sacks' => 'required|integer|min:1',
            'input_weight_kg' => 'required|numeric|min:1',
            'started_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['operator_user_id'] = Auth::id();
        $validated['status'] = 'in_progress';
        $validated['total_milled_output_kg'] = 0;
        $validated['recovery_rate_pct'] = 0;

        DB::transaction(function () use ($validated) {
            MillingBatch::create($validated);

            // Deduct raw palay stock
            $rawItem = Item::where('category', 'raw_palay')
                ->where('variety', $validated['palay_variety'])
                ->first() ?: Item::where('category', 'raw_palay')->first();

            if ($rawItem && $rawItem->current_stock >= $validated['input_sacks']) {
                $rawItem->decrement('current_stock', $validated['input_sacks']);
            }
        });

        return redirect()->route('milling-batches.index')
            ->with('success', "Milling batch #{$validated['batch_number']} started successfully.");
    }

    /**
     * Display the specified milling run and output yields.
     */
    public function show(MillingBatch $millingBatch)
    {
        $millingBatch->load(['operator', 'palayIntake', 'outputs.item']);
        $inventoryItems = Item::whereIn('category', ['milled_rice', 'by_product'])->get();

        return view('milling-batches.show', compact('millingBatch', 'inventoryItems'));
    }

    /**
     * Show the form for editing/completing a batch.
     */
    public function edit(MillingBatch $millingBatch)
    {
        $millingBatch->load('outputs.item');
        $inventoryItems = Item::whereIn('category', ['milled_rice', 'by_product'])->get();

        return view('milling-batches.edit', compact('millingBatch', 'inventoryItems'));
    }

    /**
     * Update/complete the batch with outputs.
     */
    public function update(Request $request, MillingBatch $millingBatch)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed,cancelled',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'outputs' => 'nullable|array',
            'outputs.*.item_id' => 'required_with:outputs|exists:items,id',
            'outputs.*.output_type' => 'required_with:outputs|in:milled_rice,darak_bran,binlid_broken,ipa_husk',
            'outputs.*.quantity_units' => 'required_with:outputs|numeric|min:0',
            'outputs.*.weight_kg' => 'required_with:outputs|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $millingBatch) {
            $totalOutputKg = 0;

            if (!empty($validated['outputs'])) {
                // Delete previous outputs if re-logging
                $millingBatch->outputs()->delete();

                foreach ($validated['outputs'] as $out) {
                    if ((float) $out['weight_kg'] > 0 || (float) $out['quantity_units'] > 0) {
                        $millingBatch->outputs()->create($out);
                        $totalOutputKg += (float) $out['weight_kg'];

                        // If completing batch, increment stock of outputs
                        if ($validated['status'] === 'completed') {
                            $item = Item::find($out['item_id']);
                            if ($item) {
                                $item->increment('current_stock', (float) $out['quantity_units']);
                            }
                        }
                    }
                }
            }

            $inputKg = (float) $millingBatch->input_weight_kg;
            $recoveryPct = ($inputKg > 0) ? round(($totalOutputKg / $inputKg) * 100, 2) : 0;

            $updateData = [
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'completed' ? ($validated['completed_at'] ?? now()) : $validated['completed_at'],
                'total_milled_output_kg' => $totalOutputKg,
                'recovery_rate_pct' => $recoveryPct,
                'notes' => $validated['notes'],
            ];

            $millingBatch->update($updateData);
        });

        return redirect()->route('milling-batches.show', $millingBatch)
            ->with('success', "Milling batch #{$millingBatch->batch_number} updated successfully.");
    }

    /**
     * Remove the specified milling run.
     */
    public function destroy(MillingBatch $millingBatch)
    {
        $batchNumber = $millingBatch->batch_number;
        $millingBatch->delete();

        return redirect()->route('milling-batches.index')
            ->with('success', "Milling batch #{$batchNumber} removed successfully.");
    }
}

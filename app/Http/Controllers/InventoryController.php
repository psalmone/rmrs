<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of items with stock levels, categories, and alerts.
     */
    public function index(Request $request)
    {
        $category = $request->input('category');
        $search = $request->input('search');
        $filter = $request->input('filter'); // e.g. low_stock

        $query = Item::query()->orderBy('category')->orderBy('name');

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('variety', 'like', "%{$search}%");
            });
        }

        if ($filter === 'low_stock') {
            $query->whereColumn('current_stock', '<=', 'reorder_level');
        }

        $items = $query->paginate(15)->withQueryString();

        // Stock stats
        $stats = [
            'total_items' => Item::count(),
            'raw_palay_sacks' => Item::where('category', 'raw_palay')->sum('current_stock'),
            'milled_rice_sacks' => Item::where('category', 'milled_rice')->sum('current_stock'),
            'by_product_sacks' => Item::where('category', 'by_product')->sum('current_stock'),
            'low_stock_count' => Item::whereColumn('current_stock', '<=', 'reorder_level')->count(),
        ];

        // Recent manual adjustments
        $recentAdjustments = StockAdjustment::with(['item', 'adjustedBy'])->latest()->take(6)->get();

        return view('inventory.index', compact('items', 'stats', 'category', 'search', 'filter', 'recentAdjustments'));
    }

    /**
     * Show form to create a new inventory item.
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code',
            'name' => 'required|string|max:255',
            'category' => 'required|in:raw_palay,milled_rice,by_product',
            'variety' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $item = Item::create($validated);

        // If starting stock is > 0, log initial stock adjustment entry
        if ((float) $item->current_stock > 0) {
            $count = StockAdjustment::whereYear('created_at', now()->year)->count() + 1;
            StockAdjustment::create([
                'adjustment_number' => 'ADJ-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT),
                'item_id' => $item->id,
                'type' => 'addition',
                'quantity' => $item->current_stock,
                'previous_stock' => 0,
                'new_stock' => $item->current_stock,
                'reason' => 'initial_stock',
                'adjusted_by_user_id' => Auth::id(),
                'adjustment_date' => now(),
                'notes' => 'Baseline opening inventory on creation',
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', "Item {$item->name} ({$item->code}) created successfully.");
    }

    /**
     * Show form to edit an existing inventory item.
     */
    public function edit(Item $item)
    {
        return view('inventory.edit', compact('item'));
    }

    /**
     * Update an inventory item.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'category' => 'required|in:raw_palay,milled_rice,by_product',
            'variety' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'reorder_level' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $item->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', "Item {$item->name} updated successfully.");
    }

    /**
     * Show form to record a stock adjustment (spillage, physical audit, shrinkage, damage).
     */
    public function adjustmentForm(Request $request)
    {
        $items = Item::where('is_active', true)->orderBy('name')->get();
        $selectedItemId = $request->input('item_id');

        $count = StockAdjustment::whereYear('created_at', now()->year)->count() + 1;
        $suggestedAdjustment = 'ADJ-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('inventory.adjust', compact('items', 'selectedItemId', 'suggestedAdjustment'));
    }

    /**
     * Process and commit a stock adjustment.
     */
    public function processAdjustment(Request $request)
    {
        $validated = $request->validate([
            'adjustment_number' => 'required|string|max:50|unique:stock_adjustments,adjustment_number',
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:addition,subtraction',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|in:physical_audit,spillage,bag_damage,silo_shrinkage,return_from_buyer,initial_stock,other',
            'adjustment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        $qty = (float) $validated['quantity'];
        $previousStock = (float) $item->current_stock;

        if ($validated['type'] === 'subtraction' && $qty > $previousStock) {
            return back()->withInput()->withErrors([
                'quantity' => "Cannot subtract {$qty} units. Current stock is only {$previousStock} units."
            ]);
        }

        $newStock = ($validated['type'] === 'addition') 
            ? ($previousStock + $qty) 
            : ($previousStock - $qty);

        DB::transaction(function () use ($validated, $item, $previousStock, $newStock, $qty) {
            StockAdjustment::create([
                'adjustment_number' => $validated['adjustment_number'],
                'item_id' => $item->id,
                'type' => $validated['type'],
                'quantity' => $qty,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reason' => $validated['reason'],
                'adjusted_by_user_id' => Auth::id(),
                'adjustment_date' => $validated['adjustment_date'],
                'notes' => $validated['notes'],
            ]);

            $item->update(['current_stock' => $newStock]);
        });

        return redirect()->route('inventory.index')
            ->with('success', "Stock adjustment #{$validated['adjustment_number']} applied. {$item->name} new stock: {$newStock} {$item->unit}.");
    }

    /**
     * Delete an inventory item.
     */
    public function destroy(Item $item)
    {
        $name = $item->name;
        $item->delete();

        return redirect()->route('inventory.index')
            ->with('success', "Item {$name} deleted successfully.");
    }
}

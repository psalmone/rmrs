<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\Item;
use App\Models\PalayIntake;
use App\Models\MillingBatch;
use App\Models\Customer;
use App\Models\Sale;

class DashboardController extends Controller
{
    /**
     * Display the operational management dashboard with live metrics.
     */
    public function index()
    {
        // 1. Palay Intake Metrics
        $totalPalaySacks = PalayIntake::sum('bag_count');
        $totalPalayWeightKg = PalayIntake::sum('net_weight_kg');

        // 2. Inventory Stocks
        $milledRiceStock = Item::where('category', 'milled_rice')->sum('current_stock');
        $rawPalayStock = Item::where('category', 'raw_palay')->sum('current_stock');
        $byProductStock = Item::where('category', 'by_product')->sum('current_stock');

        // 3. Milling Efficiency & Yield
        $completedBatches = MillingBatch::where('status', 'completed')->get();
        $avgRecoveryRate = $completedBatches->count() > 0 
            ? round($completedBatches->avg('recovery_rate_pct'), 1) 
            : 0;
        $totalMilledOutputKg = $completedBatches->sum('total_milled_output_kg');

        // 4. Sales & Revenue
        $totalSalesAmount = Sale::sum('net_amount');
        $totalCollected = Sale::sum('paid_amount');
        $outstandingBalances = Sale::sum('balance_amount');

        // 5. Recent Feeds
        $recentIntakes = PalayIntake::with('farmer')->latest('intake_date')->take(5)->get();
        $recentBatches = MillingBatch::with('outputs.item')->latest()->take(5)->get();
        $recentSales = Sale::with('customer')->latest('sale_date')->take(5)->get();
        $lowStockItems = Item::whereColumn('current_stock', '<=', 'reorder_level')->get();

        return view('dashboard', compact(
            'totalPalaySacks',
            'totalPalayWeightKg',
            'milledRiceStock',
            'rawPalayStock',
            'byProductStock',
            'avgRecoveryRate',
            'totalMilledOutputKg',
            'totalSalesAmount',
            'totalCollected',
            'outstandingBalances',
            'recentIntakes',
            'recentBatches',
            'recentSales',
            'lowStockItems'
        ));
    }
}

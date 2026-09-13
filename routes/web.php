<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PalayIntakeController;
use App\Http\Controllers\MillingBatchController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InventoryController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Operational Rice Mill Modules
    Route::resource('palay-intakes', PalayIntakeController::class);
    Route::resource('milling-batches', MillingBatchController::class);
    Route::resource('sales', SaleController::class);

    // Inventory & Stock Adjustments
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('inventory/{item}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('inventory/{item}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('inventory/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    Route::get('inventory-adjust', [InventoryController::class, 'adjustmentForm'])->name('inventory.adjust.form');
    Route::post('inventory-adjust', [InventoryController::class, 'processAdjustment'])->name('inventory.adjust.process');
});

require __DIR__.'/auth.php';


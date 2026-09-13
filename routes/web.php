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
});

require __DIR__.'/auth.php';


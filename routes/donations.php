<?php

use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/history', [DonationController::class, 'history'])->name('donations.history');
    Route::get('/donations/{donation}/receipt', [DonationController::class, 'receipt'])->name('donations.receipt');
    Route::get('/donations/{campaign}', [DonationController::class, 'show'])->name('donations.show');
    Route::post('/donations/{campaign}', [DonationController::class, 'store'])->name('donations.store');
});

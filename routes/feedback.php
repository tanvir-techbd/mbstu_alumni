<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    Route::get('/feedback/export', [FeedbackController::class, 'export'])
        ->middleware('role:super-admin')
        ->name('feedback.export');

    Route::get('/feedback/{ticket}', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::post('/feedback/{ticket}/reply', [FeedbackController::class, 'reply'])->name('feedback.reply');
    Route::post('/feedback/{ticket}/close', [FeedbackController::class, 'close'])->name('feedback.close');
});

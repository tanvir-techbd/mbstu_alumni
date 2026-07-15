<?php

use App\Http\Controllers\SuccessStoryController;
use App\Http\Controllers\SuccessStoryManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/success-stories', [SuccessStoryController::class, 'index'])->name('success-stories.index');

    Route::middleware('role:alumni')->group(function () {
        Route::get('/success-stories/create', [SuccessStoryManagementController::class, 'create'])->name('success-stories.create');
        Route::post('/success-stories', [SuccessStoryManagementController::class, 'store'])->name('success-stories.store');
    });

    Route::get('/success-stories/{successStory}', [SuccessStoryController::class, 'show'])->name('success-stories.show');
    Route::get('/success-stories/{successStory}/edit', [SuccessStoryManagementController::class, 'edit'])->name('success-stories.edit');
    Route::put('/success-stories/{successStory}', [SuccessStoryManagementController::class, 'update'])->name('success-stories.update');
    Route::delete('/success-stories/{successStory}', [SuccessStoryManagementController::class, 'destroy'])->name('success-stories.destroy');
    Route::delete('/success-story-images/{image}', [SuccessStoryManagementController::class, 'destroyImage'])->name('success-stories.images.destroy');

    Route::middleware('role:super-admin')->group(function () {
        Route::post('/success-stories/{successStory}/approve', [SuccessStoryManagementController::class, 'approve'])->name('success-stories.approve');
        Route::post('/success-stories/{successStory}/reject', [SuccessStoryManagementController::class, 'reject'])->name('success-stories.reject');
    });
});

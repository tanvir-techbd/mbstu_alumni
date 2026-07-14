<?php

use App\Http\Controllers\JobManagementController;
use App\Http\Controllers\JobPostingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');

    Route::middleware('role:alumni')->group(function () {
        Route::get('/jobs/create', [JobManagementController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [JobManagementController::class, 'store'])->name('jobs.store');
    });

    Route::get('/jobs/{job}', [JobPostingController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/bookmark', [JobPostingController::class, 'toggleBookmark'])->name('jobs.bookmark');

    Route::get('/jobs/{job}/edit', [JobManagementController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [JobManagementController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [JobManagementController::class, 'destroy'])->name('jobs.destroy');

    Route::middleware('role:super-admin')->group(function () {
        Route::post('/jobs/{job}/approve', [JobManagementController::class, 'approve'])->name('jobs.approve');
        Route::post('/jobs/{job}/reject', [JobManagementController::class, 'reject'])->name('jobs.reject');
    });
});

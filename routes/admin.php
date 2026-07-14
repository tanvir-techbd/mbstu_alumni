<?php

use App\Http\Controllers\Admin\AlumniVerificationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:super-admin'])->group(function () {
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class)->except('show');

    Route::prefix('alumni-verifications')->name('alumni-verifications.')->group(function () {
        Route::get('/', [AlumniVerificationController::class, 'index'])->name('index');
        Route::get('/{alumniProfile}', [AlumniVerificationController::class, 'show'])->name('show');
        Route::get('/{alumniProfile}/document', [AlumniVerificationController::class, 'downloadDocument'])->name('document');
        Route::post('/{alumniProfile}/approve', [AlumniVerificationController::class, 'approve'])->name('approve');
        Route::post('/{alumniProfile}/reject', [AlumniVerificationController::class, 'reject'])->name('reject');
    });
});

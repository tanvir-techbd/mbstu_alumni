<?php

use App\Http\Controllers\Alumni\AlumniProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('alumni')->name('alumni.')->middleware(['auth', 'verified', 'role:alumni'])->group(function () {
    Route::get('/profile', [AlumniProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AlumniProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [AlumniProfileController::class, 'uploadPhoto'])->name('profile.photo');
    Route::post('/profile/document', [AlumniProfileController::class, 'uploadDocument'])->name('profile.document');
});

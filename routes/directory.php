<?php

use App\Http\Controllers\DirectoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
    Route::get('/directory/{alumniProfile}', [DirectoryController::class, 'show'])->name('directory.show');
});

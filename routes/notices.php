<?php

use App\Http\Controllers\NoticeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');

    Route::middleware('role:super-admin|faculty')->group(function () {
        Route::get('/notices/create', [NoticeController::class, 'create'])->name('notices.create');
        Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
    });

    Route::get('/notices/{notice}', [NoticeController::class, 'show'])->name('notices.show');
    Route::get('/notices/{notice}/download', [NoticeController::class, 'download'])->name('notices.download');
    Route::post('/notices/{notice}/bookmark', [NoticeController::class, 'toggleBookmark'])->name('notices.bookmark');
    Route::get('/notices/{notice}/edit', [NoticeController::class, 'edit'])->name('notices.edit');
    Route::put('/notices/{notice}', [NoticeController::class, 'update'])->name('notices.update');
    Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
});

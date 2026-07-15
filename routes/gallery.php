<?php

use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

    Route::middleware('role:super-admin|faculty')->group(function () {
        Route::get('/gallery/create', [GalleryController::class, 'create'])->name('gallery.create');
        Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    });

    Route::get('/gallery/{gallery}', [GalleryController::class, 'show'])->name('gallery.show');

    Route::middleware('role:super-admin|faculty')->group(function () {
        Route::get('/gallery/{gallery}/edit', [GalleryController::class, 'edit'])->name('gallery.edit');
        Route::put('/gallery/{gallery}', [GalleryController::class, 'update'])->name('gallery.update');
        Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
        Route::delete('/gallery-images/{image}', [GalleryController::class, 'destroyImage'])->name('gallery.images.destroy');
    });
});

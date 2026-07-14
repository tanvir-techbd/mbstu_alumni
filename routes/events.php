<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\EventManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');

    Route::middleware('role:super-admin|faculty')->group(function () {
        Route::get('/events/create', [EventManagementController::class, 'create'])->name('events.create');
        Route::post('/events', [EventManagementController::class, 'store'])->name('events.store');
    });

    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/register', [EventController::class, 'cancelRegistration'])->name('events.cancel');

    Route::middleware('role:super-admin|faculty')->group(function () {
        Route::get('/events/{event}/edit', [EventManagementController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventManagementController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventManagementController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{event}/publish', [EventManagementController::class, 'publish'])->name('events.publish');
        Route::post('/events/{event}/archive', [EventManagementController::class, 'archive'])->name('events.archive');
        Route::get('/events/{event}/participants', [EventManagementController::class, 'participants'])->name('events.participants');
        Route::get('/events/{event}/participants/export', [EventManagementController::class, 'exportParticipants'])->name('events.participants.export');
        Route::patch('/events/{event}/participants/{user}/attendance', [EventManagementController::class, 'markAttendance'])->name('events.attendance');
    });
});

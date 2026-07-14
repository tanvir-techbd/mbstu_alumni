<?php

use App\Http\Controllers\MentorshipController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mentorship', [MentorshipController::class, 'index'])->name('mentorship.index');
    Route::post('/mentorship/mentors/{mentor}/request', [MentorshipController::class, 'store'])->name('mentorship.request');
    Route::post('/mentorship/{mentorshipRequest}/accept', [MentorshipController::class, 'accept'])->name('mentorship.accept');
    Route::post('/mentorship/{mentorshipRequest}/reject', [MentorshipController::class, 'reject'])->name('mentorship.reject');
    Route::post('/mentorship/{mentorshipRequest}/schedule', [MentorshipController::class, 'schedule'])->name('mentorship.schedule');
    Route::post('/mentorship/{mentorshipRequest}/complete', [MentorshipController::class, 'complete'])->name('mentorship.complete');
    Route::delete('/mentorship/{mentorshipRequest}', [MentorshipController::class, 'withdraw'])->name('mentorship.withdraw');
});

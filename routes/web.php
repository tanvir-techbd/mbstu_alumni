<?php

use App\Enums\EventStatus;
use App\Enums\JobStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Models\AlumniProfile;
use App\Models\Donation;
use App\Models\Event;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'stats' => [
            'alumni' => AlumniProfile::where('verification_status', VerificationStatus::Approved)->count(),
            'events' => Event::where('status', EventStatus::Published)->count(),
            'jobs' => JobPosting::where('status', JobStatus::Published)->count(),
            'donations' => Donation::sum('amount'),
        ],
    ]);
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/alumni.php';
require __DIR__.'/directory.php';
require __DIR__.'/events.php';
require __DIR__.'/jobs.php';
require __DIR__.'/mentorship.php';
require __DIR__.'/notices.php';
require __DIR__.'/success-stories.php';
require __DIR__.'/donations.php';
require __DIR__.'/gallery.php';
require __DIR__.'/documents.php';
require __DIR__.'/feedback.php';
require __DIR__.'/auth.php';

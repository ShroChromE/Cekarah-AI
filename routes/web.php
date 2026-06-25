<?php

use App\Http\Controllers\Portal\AidProgramController;
use App\Http\Controllers\Portal\ClaimVerificationController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\RadarController;
use App\Http\Controllers\Portal\ReviewController;
use App\Http\Controllers\Portal\ShelterLocationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public, no-login chat for citizens.
Route::get('/', fn () => Inertia::render('Landing'))->name('home');
Route::get('/chat', fn () => Inertia::render('Chat'))->name('chat');
Route::get('/about', fn () => Inertia::render('About'))->name('about');

// Portal Relawan — volunteers/admins only.
Route::middleware(['auth', 'role:admin,volunteer'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/radar', [RadarController::class, 'index'])->name('radar.index');

    Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
    Route::patch('/review/{log}/resolve', [ReviewController::class, 'resolve'])->name('review.resolve');

    Route::resource('shelters', ShelterLocationController::class)->except('show');
    Route::resource('aid', AidProgramController::class)->except('show');
    Route::resource('claims', ClaimVerificationController::class)->except('show');
});

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EventController;

// WelcomePageはログイン不要
Route::get('/', function () {
    return Inertia::render('WelcomePage');
});

// それ以外はすべて認証が必要
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/calendar', [EventController::class, 'show'])->name("show");
    Route::post('/calendar/create', [EventController::class, 'create'])->name('create');
    Route::post('/calendar/get', [EventController::class, 'get'])->name("get");
    Route::put('/calendar/update', [EventController::class, 'update'])->name('update');
    Route::delete('/calendar/delete', [EventController::class, 'delete'])->name("delete");
    Route::post('/calendar/analyze', [EventController::class, 'analyzeAndSave'])->name('analyze');
    Route::post('/api/speech-to-text', [EventController::class, 'transcribeAndSave']);
});

require __DIR__ . '/auth.php';

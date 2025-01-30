<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SpeechController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/calendar', [EventController::class, 'show'])->name("show");

Route::post('/calendar/create', [EventController::class, 'create'])->name('create');

Route::post('/calendar/get',  [EventController::class, 'get'])->name("get"); // DBに登録した予定を取得

Route::put('/calendar/update', [EventController::class, 'update'])->name('update');

Route::delete('/calendar/delete', [EventController::class, 'delete'])->name("delete"); // 予定の削除

Route::post('/calendar/analyze', [EventController::class, 'analyzeAndSave'])->name('analyze');

Route::post('/api/speech-to-text', [EventController::class, 'transcribeAndSave']);

require __DIR__ . '/auth.php';

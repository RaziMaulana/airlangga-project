<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackFormController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {

    Route::prefix('dashboard')->name('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('.index');
    });

    Route::prefix('report')->name('report')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('.index');
        Route::get('/export', [ReportController::class, 'export'])->name('.export');
    });

    Route::prefix('access')->name('access')->group(function () {
        Route::get('/', [AccessController::class, 'index'])->name('.index');
        Route::get('/data', [AccessController::class, 'data'])->name('.data');
        Route::get('/stats', [AccessController::class, 'stats'])->name('.stats');
        Route::post('/', [AccessController::class, 'store'])->name('.store');
        Route::get('/{user}/edit', [AccessController::class, 'edit'])->name('.edit');
        Route::put('/{user}', [AccessController::class, 'update'])->name('.update');
        Route::delete('/{user}', [AccessController::class, 'destroy'])->name('.destroy');
    });

    Route::prefix('feedback')->name('feedback')->group(function () {
        Route::get('/', [FeedbackController::class, 'index'])->name('.index');
    });
});

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/', [GuruController::class, 'index'])->name('index');
        Route::get('/dashboard', [GuruController::class, 'index'])->name('dashboard');
        Route::get('/absensi', [GuruController::class, 'absensi'])->name('absensi.index');
        Route::get('/absensi/data', [GuruController::class, 'absensiData'])->name('absensi.data');
        Route::get('/absensi/stats', [GuruController::class, 'absensiStats'])->name('absensi.stats');
    });

Route::prefix('siswa')
    ->name('siswa.')
    ->middleware(['auth', 'role:siswa'])
    ->group(function () {
        Route::get('/', [SiswaController::class, 'index'])->name('index');
        Route::get('/absensi', [SiswaController::class, 'absensi'])->name('absensi.index');
        Route::get('/absensi/data', [SiswaController::class, 'absensiData'])->name('absensi.data');
        Route::get('/absensi/stats', [SiswaController::class, 'absensiStats'])->name('absensi.stats');
    });

Route::prefix('orang-tua')
    ->name('orangtua.')
    ->middleware(['auth', 'role:orangtua'])
    ->group(function () {
        Route::get('/', [OrangTuaController::class, 'index'])->name('index');
        Route::get('/absensi/data', [OrangTuaController::class, 'absensiData'])->name('absensi.data');
        Route::get('/absensi/stats', [OrangTuaController::class, 'absensiStats'])->name('absensi.stats');
    });

Route::prefix('feedback-form')
    ->name('feedback-form.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', [FeedbackFormController::class, 'index'])->name('index');
        Route::post('/store', [FeedbackFormController::class, 'store'])->name('store');
    });

require __DIR__ . '/auth.php';

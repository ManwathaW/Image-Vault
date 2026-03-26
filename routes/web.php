<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// All image routes are protected by auth middleware
Route::middleware('auth')->group(function () {
    // Dashboard with upload form
    Route::get('/dashboard', [ImageController::class, 'index'])->name('dashboard');

    // Image upload
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');

    // Single image view & delete
    Route::get('/images/{image}', [ImageController::class, 'show'])->name('images.show');
    Route::delete('/images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');

    // Gallery view
    Route::get('/gallery', [ImageController::class, 'gallery'])->name('images.gallery');

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

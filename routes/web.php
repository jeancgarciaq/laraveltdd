<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

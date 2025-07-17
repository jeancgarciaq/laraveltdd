<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/tasks', [TaskController::class, 'store']);
// Necesario para la redirección
Route::get('/tasks', [TaskController::class, 'index']);
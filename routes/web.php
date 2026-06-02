<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/chat', [ChatController::class, 'index'])
    ->name('chat.index');

Route::get('/chat/{conversation}', [ChatController::class, 'show'])
    ->name('chat.show');

Route::post('/chat/{conversation}/send', [ChatController::class, 'send'])
    ->name('chat.send');

Route::get('/staff', [StaffController::class, 'index'])
    ->name('staff.index');

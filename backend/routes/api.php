<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => config('app.name'),
    'time' => now()->toISOString(),
]));

Route::middleware('throttle:20,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me'])->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/dashboard', DashboardController::class)->name('api.dashboard');
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('api.notifications');
    Route::get('/calendar', CalendarController::class)->name('api.calendar');

    Route::get('/profile', [ProfileController::class, 'show'])->name('api.profile.show');
    Route::match(['post', 'put'], '/profile', [ProfileController::class, 'update'])->name('api.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('api.profile.password');

    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('api.tasks.toggle');
    Route::apiResource('tasks', TaskController::class)->names([
        'index' => 'api.tasks.index',
        'store' => 'api.tasks.store',
        'show' => 'api.tasks.show',
        'update' => 'api.tasks.update',
        'destroy' => 'api.tasks.destroy',
    ]);
});

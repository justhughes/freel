<?php

use App\Http\Controllers\Api\AdminRevisionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FreelancerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::middleware('role:client,freelancer,admin')->group(function () {
        Route::get('/packages', [PackageController::class, 'index']);
        Route::get('/packages/{package}', [PackageController::class, 'show']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/packages', [PackageController::class, 'store']);
        Route::put('/packages/{package}', [PackageController::class, 'update']);
        Route::patch('/packages/{package}', [PackageController::class, 'update']);
        Route::delete('/packages/{package}', [PackageController::class, 'destroy']);
    });

    Route::middleware('role:client,admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
    });

    Route::middleware('role:client')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
        Route::put('/orders/{order}', [OrderController::class, 'update']);
        Route::patch('/orders/{order}', [OrderController::class, 'update']);
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

        Route::get('/orders/{order}/payment', [PaymentController::class, 'show']);
        Route::post('/orders/{order}/payment', [PaymentController::class, 'store']);
        Route::post('/payments/{payment}/upload-proof', [PaymentController::class, 'uploadProof']);
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify']);

        Route::get('/client/orders/{order}', [ClientController::class, 'show']);
        Route::post('/client/orders/{order}/revision', [ClientController::class, 'requestRevision']);
        Route::post('/client/orders/{order}/approve', [ClientController::class, 'approve']);
    });

    Route::middleware('role:freelancer')->prefix('freelancer')->group(function () {
        Route::get('/jobs', [FreelancerController::class, 'jobs']);
        Route::post('/jobs/{order}/take', [FreelancerController::class, 'take']);
        Route::get('/tasks', [FreelancerController::class, 'tasks']);
        Route::get('/tasks/{order}', [FreelancerController::class, 'showTask']);
        Route::post('/tasks/{order}/submit', [FreelancerController::class, 'submit']);
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/revisions', [AdminRevisionController::class, 'index']);
        Route::post('/revisions/{revision}/forward', [AdminRevisionController::class, 'forward']);
        Route::post('/revisions/{revision}/reject', [AdminRevisionController::class, 'reject']);
    });
});

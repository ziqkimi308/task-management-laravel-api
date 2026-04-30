<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public
Route::prefix('v1')->group(function () {
	Route::post('/register', [AuthController::class, 'register']);
	Route::post('/login', [AuthController::class, 'login']);
});

// Protected
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
	// Auth - user
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/me', [AuthController::class, 'me']);
	Route::get('/dashboard', [AuthController::class, 'dashboard']);

	// Projects
	Route::get('/projects/stats', [ProjectController::class, 'stats']);
	Route::get('/projects/trashed', [ProjectController::class, 'trashed']);
	Route::post('/projects/{project}/restore', [ProjectController::class, 'restore']);
	Route::apiResource('projects', ProjectController::class);
	
	// Tasks
	Route::put('/tasks/bulk', [TaskController::class, 'bulkUpdate']);
	Route::apiResource('tasks', TaskController::class);
});
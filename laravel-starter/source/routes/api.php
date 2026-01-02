<?php

use App\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;
use App\Controllers\API\ReminderController;

/**
 * Use this file to define new API routes under the /api/... path
 * 
 * Here are some example, user related endpoints we have established as an example
 */

Route::get('/users/{id}', [UserController::class, 'read']);
Route::post('/users', [UserController::class, 'create']);

// Reminder routes

Route::get("/reminders", [ReminderController::class, 'index']);
Route::get("/reminders/search", [ReminderController::class, 'search']);
Route::get("/reminders/{id}", [ReminderController::class, 'show']);
Route::post("/reminders", [ReminderController::class, 'store']);
Route::put("/reminders/{id}", [ReminderController::class, 'update']);
Route::delete("/reminders/{id}", [ReminderController::class, 'destroy']);


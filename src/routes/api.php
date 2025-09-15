<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/todos', [TodoController::class, 'index']);
Route::post('/todos', [TodoController::class, 'store']);
Route::get('/todos/{id}', [TodoController::class, 'show'])->whereUuid('id');
Route::patch('/todos/{id}', [TodoController::class, 'update'])->whereUuid('id');
Route::delete('/todos/{id}', [TodoController::class, 'destroy'])->whereUuid('id');

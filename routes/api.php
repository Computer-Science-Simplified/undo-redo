<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/todos', [TodoController::class, 'store']);
    Route::patch('/todos/{todo}/description', [TodoController::class, 'updateDescription']);
    Route::patch('/todos/{todo}/assignee', [TodoController::class, 'updateAssignee']);
    Route::patch('/todos/{todoId}/undo', [TodoController::class, 'undo']);
    Route::patch('/todos/{todoId}/redo', [TodoController::class, 'redo']);
});

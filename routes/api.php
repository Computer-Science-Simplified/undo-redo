<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::get('/todos/{todo}', [TodoController::class, 'show'])->name('todos.show');
    Route::patch('/todos/{todo}/description', [TodoController::class, 'updateDescription'])->name('todos.update-description');
    Route::patch('/todos/{todo}/assignee', [TodoController::class, 'updateAssignee'])->name('todos.update-assignee');
    Route::patch('/todos/{todoId}/undo', [TodoController::class, 'undo'])->name('todos.undo');
    Route::patch('/todos/{todoId}/redo', [TodoController::class, 'redo'])->name('todos.redo');
});

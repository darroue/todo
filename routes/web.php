<?php

use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\Todos\TaskController;
use App\Http\Controllers\Todos\TodoController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');

        Route::get('todos', [TodoController::class, 'index'])->name('todos.index');
        Route::post('todos', [TodoController::class, 'store'])->name('todos.store');
        Route::get('todos/{todo}', [TodoController::class, 'show'])->name('todos.show');
        Route::delete('todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
        Route::post('todos/{todo}/restore', [TodoController::class, 'restore'])->name('todos.restore');

        Route::post('todos/{todo}/tasks', [TaskController::class, 'store'])->name('todos.tasks.store');
        Route::patch('todos/{todo}/tasks/{task}', [TaskController::class, 'update'])->name('todos.tasks.update');
        Route::delete('todos/{todo}/tasks/{task}', [TaskController::class, 'destroy'])->name('todos.tasks.destroy');
        Route::post('todos/{todo}/tasks/{task}/restore', [TaskController::class, 'restore'])->name('todos.tasks.restore');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/settings.php';

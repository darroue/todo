<?php

use App\Models\Task;
use App\Models\Todo;
use App\Models\User;

test('task can be created for a todo', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.store', [$team->slug, $todo->id]), [
            'title' => 'Write tests',
            'description' => 'Make sure everything works',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'todo_id' => $todo->id,
        'title' => 'Write tests',
        'description' => 'Make sure everything works',
        'completed_at' => null,
    ]);
});

test('task title is required', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.store', [$team->slug, $todo->id]), ['title' => ''])
        ->assertSessionHasErrors('title');
});

test('task can be marked as completed', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->patch(route('todos.tasks.update', [$team->slug, $todo->id, $task->id]), [
            'isCompleted' => true,
        ])
        ->assertRedirect();

    $this->assertNotNull($task->fresh()->completed_at);
});

test('task can be marked as incomplete', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->completed()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->patch(route('todos.tasks.update', [$team->slug, $todo->id, $task->id]), [
            'isCompleted' => false,
        ])
        ->assertRedirect();

    $this->assertNull($task->fresh()->completed_at);
});

test('task can be deleted', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->delete(route('todos.tasks.destroy', [$team->slug, $todo->id, $task->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
});

test('task can be restored', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);
    $task->delete();

    $this->actingAs($user)
        ->post(route('todos.tasks.restore', [$team->slug, $todo->id, $task->id]))
        ->assertRedirect();

    $this->assertNotSoftDeleted('tasks', ['id' => $task->id]);
});

test('task from different todo returns 404', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo1 = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $todo2 = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo2->id]);

    $this->actingAs($user)
        ->delete(route('todos.tasks.destroy', [$team->slug, $todo1->id, $task->id]))
        ->assertNotFound();
});

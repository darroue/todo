<?php

use App\Enums\TeamRole;
use App\Models\Membership;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Todo;
use App\Models\User;

test('comment can be added to a task', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.comments.store', [$team->slug, $todo->id, $task->id]), [
            'body' => 'This is a comment.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('task_comments', [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'body' => 'This is a comment.',
    ]);
});

test('comment body is required', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.comments.store', [$team->slug, $todo->id, $task->id]), ['body' => ''])
        ->assertSessionHasErrors('body');
});

test('comment body cannot exceed 1000 characters', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.comments.store', [$team->slug, $todo->id, $task->id]), [
            'body' => str_repeat('a', 1001),
        ])
        ->assertSessionHasErrors('body');
});

test('user can delete own comment', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);
    $comment = TaskComment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('todos.tasks.comments.destroy', [$team->slug, $todo->id, $task->id, $comment->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('task_comments', ['id' => $comment->id]);
});

test('user cannot delete another users comment', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $team = $owner->currentTeam;
    Membership::create(['team_id' => $team->id, 'user_id' => $other->id, 'role' => TeamRole::Member]);
    $other->update(['current_team_id' => $team->id]);

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $owner->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);
    $comment = TaskComment::factory()->create(['task_id' => $task->id, 'user_id' => $owner->id]);

    $this->actingAs($other)
        ->delete(route('todos.tasks.comments.destroy', [$team->slug, $todo->id, $task->id, $comment->id]))
        ->assertForbidden();

    $this->assertDatabaseHas('task_comments', ['id' => $comment->id]);
});

test('comment cannot be added to a completed task', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->completed()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.comments.store', [$team->slug, $todo->id, $task->id]), [
            'body' => 'This is a comment.',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('task_comments', ['task_id' => $task->id]);
});

test('comment cannot be deleted from a completed task', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->completed()->create(['todo_id' => $todo->id]);
    $comment = TaskComment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('todos.tasks.comments.destroy', [$team->slug, $todo->id, $task->id, $comment->id]))
        ->assertForbidden();

    $this->assertDatabaseHas('task_comments', ['id' => $comment->id]);
});

test('comments appear in todo show response', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);
    TaskComment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id, 'body' => 'Hello!']);

    $this->actingAs($user)
        ->get(route('todos.show', [$team->slug, $todo->id]))
        ->assertInertia(fn ($page) => $page
            ->component('todos/Show')
            ->where('tasks.0.comments.0.body', 'Hello!')
            ->where('tasks.0.comments.0.user.id', $user->id)
        );
});

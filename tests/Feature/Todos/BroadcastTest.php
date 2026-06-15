<?php

use App\Events\Todos\TaskChanged;
use App\Events\Todos\TaskCommentChanged;
use App\Events\Todos\TodoChanged;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Event;

test('TodoChanged broadcasts on the team private channel with payload', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);

    $event = new TodoChanged($team, $todo, 'created');

    expect($event->broadcastOn())->toEqual([new PrivateChannel('team.'.$team->id)]);
    expect($event->broadcastWith())->toBe([
        'action' => 'created',
        'todo' => [
            'id' => $todo->id,
            'title' => $todo->title,
        ],
    ]);
});

test('TaskChanged broadcasts on the team private channel with payload', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->completed()->create(['todo_id' => $todo->id]);

    $event = new TaskChanged($team, $todo, $task, 'updated');

    expect($event->broadcastOn())->toEqual([new PrivateChannel('team.'.$team->id)]);
    expect($event->broadcastWith())->toBe([
        'action' => 'updated',
        'todoId' => $todo->id,
        'task' => [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'isCompleted' => true,
            'completedAt' => $task->completed_at->toISOString(),
        ],
    ]);
});

test('TaskCommentChanged broadcasts on the team private channel with payload', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);
    $comment = TaskComment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

    $event = new TaskCommentChanged($team, $todo, $task, $comment, 'created');

    expect($event->broadcastOn())->toEqual([new PrivateChannel('team.'.$team->id)]);
    expect($event->broadcastWith())->toBe([
        'action' => 'created',
        'todoId' => $todo->id,
        'taskId' => $task->id,
        'comment' => [
            'id' => $comment->id,
            'body' => $comment->body,
            'createdAt' => $comment->created_at->toISOString(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
        ],
    ]);
});

test('creating a todo dispatches TodoChanged', function () {
    Event::fake([TodoChanged::class]);

    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('todos.store', $team->slug), ['title' => 'Broadcast me'])
        ->assertRedirect();

    Event::assertDispatched(TodoChanged::class, fn (TodoChanged $event) => $event->action === 'created'
        && $event->team->is($team));
});

test('creating a task dispatches TaskChanged', function () {
    Event::fake([TaskChanged::class]);

    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.store', [$team->slug, $todo->id]), ['title' => 'Broadcast task'])
        ->assertRedirect();

    Event::assertDispatched(TaskChanged::class, fn (TaskChanged $event) => $event->action === 'created'
        && $event->todo->is($todo));
});

test('creating a comment dispatches TaskCommentChanged', function () {
    Event::fake([TaskCommentChanged::class]);

    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.comments.store', [$team->slug, $todo->id, $task->id]), [
            'body' => 'Nice work',
        ])
        ->assertRedirect();

    Event::assertDispatched(TaskCommentChanged::class, fn (TaskCommentChanged $event) => $event->action === 'created'
        && $event->task->is($task));
});

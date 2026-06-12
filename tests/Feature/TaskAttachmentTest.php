<?php

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake();
});

test('attachment can be uploaded to a task', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $file = UploadedFile::fake()->image('photo.jpg');

    $this->actingAs($user)
        ->post(route('todos.tasks.attachments.store', [$team->slug, $todo->id, $task->id]), [
            'file' => $file,
        ])
        ->assertRedirect();

    $attachment = $task->attachments()->first();

    expect($attachment)->not->toBeNull()
        ->and($attachment->filename)->toBe('photo.jpg');

    Storage::assertExists($attachment->path);
});

test('attachment can be deleted', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $file = UploadedFile::fake()->image('photo.jpg');
    $path = $file->store("attachments/tasks/{$task->id}");

    $attachment = TaskAttachment::factory()->create([
        'task_id' => $task->id,
        'path' => $path,
        'filename' => 'photo.jpg',
        'mime_type' => 'image/jpeg',
        'size' => $file->getSize(),
    ]);

    $this->actingAs($user)
        ->delete(route('todos.tasks.attachments.destroy', [$team->slug, $todo->id, $task->id, $attachment->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('task_attachments', ['id' => $attachment->id]);
    Storage::assertMissing($path);
});

test('attachment cannot be uploaded to a completed task', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->completed()->create(['todo_id' => $todo->id]);

    $file = UploadedFile::fake()->image('photo.jpg');

    $this->actingAs($user)
        ->post(route('todos.tasks.attachments.store', [$team->slug, $todo->id, $task->id]), [
            'file' => $file,
        ])
        ->assertForbidden();

    expect($task->attachments()->count())->toBe(0);
});

test('attachment cannot be deleted from a completed task', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->completed()->create(['todo_id' => $todo->id]);

    $file = UploadedFile::fake()->image('photo.jpg');
    $path = $file->store("attachments/tasks/{$task->id}");

    $attachment = TaskAttachment::factory()->create([
        'task_id' => $task->id,
        'path' => $path,
        'filename' => 'photo.jpg',
        'mime_type' => 'image/jpeg',
        'size' => $file->getSize(),
    ]);

    $this->actingAs($user)
        ->delete(route('todos.tasks.attachments.destroy', [$team->slug, $todo->id, $task->id, $attachment->id]))
        ->assertForbidden();

    $this->assertDatabaseHas('task_attachments', ['id' => $attachment->id]);
    Storage::assertExists($path);
});

test('file is required when uploading attachment', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $task = Task::factory()->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->post(route('todos.tasks.attachments.store', [$team->slug, $todo->id, $task->id]), [])
        ->assertSessionHasErrors('file');
});

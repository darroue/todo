<?php

use App\Enums\TeamRole;
use App\Models\Task;
use App\Models\Team;
use App\Models\Todo;
use App\Models\User;

test('todos index page renders for team member', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('todos.index', $team->slug))
        ->assertOk();
});

test('unauthenticated user cannot access todos', function () {
    $team = Team::factory()->create();

    $this->get(route('todos.index', $team->slug))
        ->assertRedirect(route('login'));
});

test('user cannot access todos of team they are not member of', function () {
    $user = User::factory()->create();
    $otherTeam = Team::factory()->create();

    $this->actingAs($user)
        ->get(route('todos.index', $otherTeam->slug))
        ->assertForbidden();
});

test('todo can be created', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('todos.store', $team->slug), ['title' => 'My First Todo'])
        ->assertRedirect();

    $this->assertDatabaseHas('todos', [
        'team_id' => $team->id,
        'user_id' => $user->id,
        'title' => 'My First Todo',
    ]);
});

test('todo title is required', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('todos.store', $team->slug), ['title' => ''])
        ->assertSessionHasErrors('title');
});

test('todo show page renders with tasks', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    Task::factory()->count(2)->create(['todo_id' => $todo->id]);

    $this->actingAs($user)
        ->get(route('todos.show', [$team->slug, $todo->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('todos/Show')
            ->has('tasks', 2)
        );
});

test('todo cannot be accessed from another team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $otherTeam = Team::factory()->create();
    $otherTeam->members()->attach($user, ['role' => TeamRole::Member->value]);

    $todo = Todo::factory()->create(['team_id' => $otherTeam->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('todos.show', [$team->slug, $todo->id]))
        ->assertNotFound();
});

test('todo can be deleted', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('todos.destroy', [$team->slug, $todo->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('todos', ['id' => $todo->id]);
});

test('todo can be restored', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $todo = Todo::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
    $todo->delete();

    $this->actingAs($user)
        ->post(route('todos.restore', [$team->slug, $todo->id]))
        ->assertRedirect();

    $this->assertNotSoftDeleted('todos', ['id' => $todo->id]);
});

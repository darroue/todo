<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

function todosPresenceCallback(): callable
{
    return Broadcast::driver()->getChannels()->get('todos-presence.{teamId}');
}

test('the todos presence channel is registered', function () {
    expect(todosPresenceCallback())->not->toBeNull();
});

test('a team member is authorized and receives their user data', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    expect(todosPresenceCallback()($user, (string) $team->id))->toBe([
        'id' => $user->id,
        'name' => $user->name,
    ]);
});

test('a non member is denied on the todos presence channel', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    expect(todosPresenceCallback()($outsider, (string) $team->id))->toBeNull();
});

<?php

use App\Models\Team;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    $team = Team::find($teamId);

    return $team && $user->belongsToTeam($team);
});

Broadcast::channel('todos-presence.{teamId}', function ($user, $teamId) {
    $team = Team::find($teamId);

    if (! $team || ! $user->belongsToTeam($team)) {
        return null;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Team $currentTeam): Response
    {
        $todosCount = $currentTeam->todos()->count();

        $tasksForTeam = fn (): Builder => Task::whereHas(
            'todo',
            fn (Builder $query) => $query->where('team_id', $currentTeam->id),
        );

        $tasksCount = $tasksForTeam()->count();
        $completedTasksCount = $tasksForTeam()->whereNotNull('completed_at')->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'todosCount' => $todosCount,
                'tasksCount' => $tasksCount,
                'completedTasksCount' => $completedTasksCount,
            ],
        ]);
    }
}

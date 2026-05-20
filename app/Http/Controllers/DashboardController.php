<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Team $currentTeam): Response
    {
        $todosCount = $currentTeam->todos()->count();
        $tasksCount = $currentTeam->todos()->withCount('tasks')->get()->sum('tasks_count');
        $completedTasksCount = $currentTeam->todos()
            ->withCount(['tasks as completed_tasks_count' => fn ($q) => $q->whereNotNull('completed_at')])
            ->get()
            ->sum('completed_tasks_count');

        return Inertia::render('Dashboard', [
            'stats' => [
                'todosCount' => $todosCount,
                'tasksCount' => $tasksCount,
                'completedTasksCount' => $completedTasksCount,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TodoChanged;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    public function index(Request $request, Team $currentTeam): Response
    {
        $todos = $currentTeam->todos()
            ->withCount(['tasks', 'tasks as completed_tasks_count' => fn ($q) => $q->whereNotNull('completed_at')])
            ->latest()
            ->get()
            ->map(fn (Todo $todo) => [
                'id' => $todo->id,
                'title' => $todo->title,
                'tasksCount' => $todo->tasks_count,
                'completedTasksCount' => $todo->completed_tasks_count,
                'createdAt' => $todo->created_at->toISOString(),
            ]);

        return Inertia::render('todos/Index', [
            'todos' => $todos,
        ]);
    }

    public function store(Request $request, Team $currentTeam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $todo = $currentTeam->todos()->create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
        ]);

        broadcast(new TodoChanged($currentTeam, $todo, 'created'))->toOthers();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Todo created.')]);

        return to_route('todos.show', ['current_team' => $currentTeam->slug, 'todo' => $todo->id]);
    }

    public function show(Team $currentTeam, Todo $todo): Response
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);

        return Inertia::render('todos/Show', [
            'todo' => [
                'id' => $todo->id,
                'title' => $todo->title,
                'createdAt' => $todo->created_at->toISOString(),
            ],
            'tasks' => $todo->tasks()->orderBy('created_at')->get()->map(fn ($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'isCompleted' => $task->completed_at !== null,
                'completedAt' => $task->completed_at?->toISOString(),
            ]),
        ]);
    }

    public function destroy(Team $currentTeam, Todo $todo): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);

        broadcast(new TodoChanged($currentTeam, $todo, 'deleted'))->toOthers();

        $todo->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Todo deleted.'),
            'action' => [
                'label' => __('Undo'),
                'url' => route('todos.restore', ['current_team' => $currentTeam->slug, 'todo' => $todo->id]),
            ],
        ]);

        return to_route('todos.index', ['current_team' => $currentTeam->slug]);
    }

    public function restore(Team $currentTeam, int $todo): RedirectResponse
    {
        $todo = $currentTeam->todos()->withTrashed()->findOrFail($todo);

        abort_unless($todo->team_id === $currentTeam->id, 404);

        $todo->restore();

        broadcast(new TodoChanged($currentTeam, $todo, 'restored'))->toOthers();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Todo restored.')]);

        return to_route('todos.index', ['current_team' => $currentTeam->slug]);
    }
}

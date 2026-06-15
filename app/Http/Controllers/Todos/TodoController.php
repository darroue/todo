<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TodoChanged;
use App\Http\Controllers\Controller;
use App\Http\Resources\Todos\TaskResource;
use App\Http\Resources\Todos\TodoResource;
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
            ->get();

        return Inertia::render('todos/Index', [
            'todos' => TodoResource::collection($todos),
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('flash.todo_created')]);

        return to_route('todos.show', ['current_team' => $currentTeam->slug, 'todo' => $todo->id]);
    }

    public function show(Team $currentTeam, Todo $todo): Response
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);

        return Inertia::render('todos/Show', [
            'todo' => new TodoResource($todo),
            'tasks' => TaskResource::collection(
                $todo->tasks()->with(['attachments', 'comments.user'])->orderBy('order')->get()
            ),
        ]);
    }

    public function destroy(Team $currentTeam, Todo $todo): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);

        broadcast(new TodoChanged($currentTeam, $todo, 'deleted'))->toOthers();

        $todo->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('flash.todo_deleted'),
            'action' => [
                'label' => __('flash.undo'),
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('flash.todo_restored')]);

        return to_route('todos.index', ['current_team' => $currentTeam->slug]);
    }
}

<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TaskChanged;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Team;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function store(Request $request, Team $currentTeam, Todo $todo): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $task = $todo->tasks()->create($validated);

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'created'))->toOthers();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Task added.')]);

        return back();
    }

    public function update(Request $request, Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);
        abort_unless($task->todo_id === $todo->id, 404);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isCompleted' => ['sometimes', 'boolean'],
        ]);

        if (isset($validated['isCompleted'])) {
            $task->completed_at = $validated['isCompleted'] ? now() : null;
        }

        if (isset($validated['title'])) {
            $task->title = $validated['title'];
        }

        if (array_key_exists('description', $validated)) {
            $task->description = $validated['description'];
        }

        $task->save();

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'updated'))->toOthers();

        return back();
    }

    public function destroy(Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);
        abort_unless($task->todo_id === $todo->id, 404);

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'deleted'))->toOthers();

        $task->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Task deleted.'),
            'action' => [
                'label' => __('Undo'),
                'url' => route('todos.tasks.restore', ['current_team' => $currentTeam->slug, 'todo' => $todo->id, 'task' => $task->id]),
            ],
        ]);

        return back();
    }

    public function restore(Team $currentTeam, Todo $todo, int $task): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);

        $task = $todo->tasks()->withTrashed()->findOrFail($task);

        abort_unless($task->todo_id === $todo->id, 404);

        $task->restore();

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'restored'))->toOthers();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Task restored.')]);

        return back();
    }
}

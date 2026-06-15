<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TaskChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Todos\ReorderTasksRequest;
use App\Http\Requests\Todos\StoreTaskRequest;
use App\Http\Requests\Todos\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Team;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, Team $currentTeam, Todo $todo): RedirectResponse
    {
        $validated = $request->validated();

        $task = $todo->tasks()->create([
            ...$validated,
            'order' => $todo->tasks()->max('order') + 1,
        ]);

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'created'))->toOthers();

        Inertia::toast(__('flash.task_added'));

        return back();
    }

    public function update(UpdateTaskRequest $request, Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        $validated = $request->validated();

        if (isset($validated['isCompleted'])) {
            $task->markCompleted($validated['isCompleted']);
        }

        $task->fill($request->safe()->only(['title']));

        if (array_key_exists('description', $validated)) {
            $task->description = $validated['description'];
        }

        $task->save();

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'updated'))->toOthers();

        return back();
    }

    public function destroy(Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        broadcast(new TaskChanged($currentTeam, $todo, $task, 'deleted'))->toOthers();

        $task->delete();

        Inertia::toast(__('flash.task_deleted'), action: [
            'label' => __('flash.undo'),
            'url' => route('todos.tasks.restore', ['current_team' => $currentTeam->slug, 'todo' => $todo->id, 'task' => $task->id]),
        ]);

        return back();
    }

    public function reorder(ReorderTasksRequest $request, Team $currentTeam, Todo $todo): RedirectResponse
    {
        $validated = $request->validated();

        foreach ($validated['ids'] as $order => $id) {
            $todo->tasks()->where('id', $id)->update(['order' => $order]);
        }

        $anyTask = $todo->tasks()->first();
        if ($anyTask) {
            broadcast(new TaskChanged($currentTeam, $todo, $anyTask, 'reordered'))->toOthers();
        }

        return back();
    }

    public function restore(Team $currentTeam, Todo $todo, int $task): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);

        $task = $todo->tasks()->withTrashed()->findOrFail($task);

        abort_unless($task->todo_id === $todo->id, 404);

        $task->restore();

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'restored'))->toOthers();

        Inertia::toast(__('flash.task_restored'));

        return back();
    }
}

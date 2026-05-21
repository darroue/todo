<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TaskCommentChanged;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Team;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskCommentController extends Controller
{
    public function store(Request $request, Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);
        abort_unless($task->todo_id === $todo->id, 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        $comment->load('user');

        broadcast(new TaskCommentChanged($currentTeam, $todo, $task, $comment, 'created'))->toOthers();

        return back();
    }

    public function destroy(Team $currentTeam, Todo $todo, Task $task, TaskComment $comment): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);
        abort_unless($task->todo_id === $todo->id, 404);
        abort_unless($comment->task_id === $task->id, 404);
        abort_unless($comment->user_id === auth()->id(), 403);

        broadcast(new TaskCommentChanged($currentTeam, $todo, $task, $comment, 'deleted'))->toOthers();

        $comment->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('flash.comment_deleted')]);

        return back();
    }
}

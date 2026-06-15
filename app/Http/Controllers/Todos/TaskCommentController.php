<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TaskCommentChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Todos\StoreTaskCommentRequest;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Team;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class TaskCommentController extends Controller
{
    public function store(StoreTaskCommentRequest $request, Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        abort_if($task->isCompleted(), 403);

        $validated = $request->validated();

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
        abort_unless($comment->user_id === auth()->id(), 403);
        abort_if($task->isCompleted(), 403);

        broadcast(new TaskCommentChanged($currentTeam, $todo, $task, $comment, 'deleted'))->toOthers();

        $comment->delete();

        Inertia::toast(__('flash.comment_deleted'));

        return back();
    }
}

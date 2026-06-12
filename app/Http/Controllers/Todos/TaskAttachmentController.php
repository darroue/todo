<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TaskChanged;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\Team;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);
        abort_unless($task->todo_id === $todo->id, 404);
        abort_if($task->isCompleted(), 403);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $file = $request->file('file');
        $path = $file->store("attachments/tasks/{$task->id}");

        $task->attachments()->create([
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'attachment_added'))->toOthers();

        return back();
    }

    public function destroy(Team $currentTeam, Todo $todo, Task $task, TaskAttachment $attachment): RedirectResponse
    {
        abort_unless($todo->team_id === $currentTeam->id, 404);
        abort_unless($task->todo_id === $todo->id, 404);
        abort_unless($attachment->task_id === $task->id, 404);
        abort_if($task->isCompleted(), 403);

        Storage::delete($attachment->path);
        $attachment->delete();

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'attachment_deleted'))->toOthers();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('flash.attachment_deleted')]);

        return back();
    }
}

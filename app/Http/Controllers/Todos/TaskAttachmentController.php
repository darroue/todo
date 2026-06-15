<?php

namespace App\Http\Controllers\Todos;

use App\Events\Todos\TaskChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Todos\StoreTaskAttachmentRequest;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\Team;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TaskAttachmentController extends Controller
{
    public function store(StoreTaskAttachmentRequest $request, Team $currentTeam, Todo $todo, Task $task): RedirectResponse
    {
        abort_if($task->isCompleted(), 403);

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
        abort_if($task->isCompleted(), 403);

        Storage::delete($attachment->path);
        $attachment->delete();

        broadcast(new TaskChanged($currentTeam, $todo, $task, 'attachment_deleted'))->toOthers();

        Inertia::toast(__('flash.attachment_deleted'));

        return back();
    }
}

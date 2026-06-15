<?php

namespace App\Http\Resources\Todos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'createdAt' => $this->created_at->toISOString(),
            'tasksCount' => $this->whenCounted('tasks'),
            'completedTasksCount' => $this->whenCounted('completed_tasks_count'),
        ];
    }
}

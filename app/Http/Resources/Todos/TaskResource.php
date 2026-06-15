<?php

namespace App\Http\Resources\Todos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'description' => $this->description,
            'isCompleted' => $this->completed_at !== null,
            'completedAt' => $this->completed_at?->toISOString(),
            'attachments' => TaskAttachmentResource::collection($this->whenLoaded('attachments')),
            'comments' => TaskCommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}

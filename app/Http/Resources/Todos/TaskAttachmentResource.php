<?php

namespace App\Http\Resources\Todos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskAttachmentResource extends JsonResource
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
            'filename' => $this->filename,
            'mimeType' => $this->mime_type,
            'size' => $this->size,
            'url' => $this->url(),
            'isImage' => $this->isImage(),
            'extension' => $this->extension(),
        ];
    }
}

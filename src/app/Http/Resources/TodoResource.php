<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => (string) $this->title,
            'completed' => (bool) $this->completed,
            'createdAt' => optional($this->created_at)->toIso8601String(),
        ];
    }
}

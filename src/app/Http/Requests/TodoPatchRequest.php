<?php

namespace App\Http\Requests;

class TodoPatchRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:1'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}

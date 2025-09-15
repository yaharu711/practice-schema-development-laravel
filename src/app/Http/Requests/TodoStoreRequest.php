<?php

namespace App\Http\Requests;

class TodoStoreRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}

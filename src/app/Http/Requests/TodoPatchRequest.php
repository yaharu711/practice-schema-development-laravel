<?php

namespace App\Http\Requests;
use App\Rules\StrictBoolean;

class TodoPatchRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $title = $this->input('title');
            if (is_string($title)) {
                // 制御文字と改行を除去
                $title = preg_replace('/[\p{Cc}\p{Cf}]+/u', '', $title);
                $title = str_replace(["\r", "\n"], '', $title);
                $this->merge(['title' => $title]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // PATCH は部分更新: 空白のみを禁止（制御文字・改行は prepareForValidation で除去）
            'title' => ['required', 'string', 'min:1', 'max:500', 'regex:/\S/u'],
            // OpenAPI の boolean に合わせ、JSON の true/false のみ許容
            'completed' => ['required', new StrictBoolean()],
        ];
    }
}

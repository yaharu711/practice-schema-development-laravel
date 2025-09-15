<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        // バリデーション失敗時は 400 を返す（スキーマ外の422を避ける）
        $response = response()->json([
            'message' => 'Bad Request',
            'errors' => $validator->errors(),
        ], 400);

        throw new HttpResponseException($response);
    }
}


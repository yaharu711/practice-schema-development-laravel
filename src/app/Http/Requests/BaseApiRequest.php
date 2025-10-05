<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\MessageBag;

abstract class BaseApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $details = $this->formatErrors($validator->errors());
        $response = response()->json([
            'message' => 'Bad Request',
            'details' => $details,
        ], 400);

        throw new HttpResponseException($response);
    }

    protected function formatErrors(MessageBag $bag): array
    {
        $out = [];
        foreach ($bag->toArray() as $field => $messages) {
            $property = $this->normalizeProperty($field);
            foreach ((array) $messages as $msg) {
                $out[] = [
                    'message' => (string) $msg,
                    'property' => $property,
                ];
            }
        }
        return $out;
    }

    protected function normalizeProperty(string $field): string
    {
        $parts = explode('.', $field);
        if (count($parts) === 1) {
            return $field;
        }
        $normalized = '';
        foreach ($parts as $index => $part) {
            if (is_numeric($part)) {
                $normalized .= "[{$part}]";
            } else {
                $normalized .= ($index === 0 ? $part : ".{$part}");
            }
        }
        return $normalized;
    }
}

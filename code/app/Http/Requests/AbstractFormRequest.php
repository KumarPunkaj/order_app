<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AbstractFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        //Currectly considering only first error
        $firstError = array_values($errors)[0][0];

        throw new HttpResponseException(response()->json([
            'error' => $firstError
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}

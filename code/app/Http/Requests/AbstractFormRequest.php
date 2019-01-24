<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Response\Response;

class AbstractFormRequest extends FormRequest
{
    /** @var Response */
    protected $responseHelper;

    /**
     * @param Response $responseHelper
     */
    public function __construct(Response $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        //Currectly considering only first error
        $firstError = array_values($errors)[0][0];

        throw new HttpResponseException(
            $this->responseHelper->setError($firstError, JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}

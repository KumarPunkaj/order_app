<?php

namespace App\Http\Requests;

use App\Http\Models\Order;

class OrderIndexRequest extends AbstractFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => [
                'required',
                'int',
                'min:1',
            ],
            'limit' => [
                'required',
                'int',
                'min:1',
            ]
        ];
    }

        /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'page.required' => 'Missing Page Parameter',
            'page.integer' => 'Invalid Page Type',
            'page.min' => 'Minimum page no must be 1',
            'limit.required' => 'Missing Limit Parameter',
            'limit.integer' => 'Invalid Limit Type',
            'limit.min' => 'Minimum limit must be 1',
        ];
    }
}

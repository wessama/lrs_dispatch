<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Support\Facades\Auth;

class EnrollUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'users' => [
                'array',
                'required',
                'min:1'
            ],
            '*.*.username' => [
                'string',
                'required'
            ],
            '*.*.oid' => [
                'string',
                'required'
            ],
            '*.*.apn' => [
                'email',
                'required'
            ],
            '*.*.catalog' => [
                'integer',
                'required'
            ],
            '*.*.access_duration' => [
                'integer',
                'required'
            ]
        ];
    }
}

<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Auth;

class EmitStatementRequest extends FormRequest
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
            'payload' => [
                'required',
                'array'
            ],
            '*.timestamp' => [
                'required'
            ],
            '*.actor' => [
                'required'
            ],
            '*.verb' => [
                'required'
            ],
            '*.object' => [
                'required'
            ],
            '*.context' => [
                'required'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'payload.required' => 'Payload must be provided',
            '*.timestamp.required'  => 'Timestamp must be provided',
            '*.actor.required'  => 'Actor must be provided',
            '*.verb.required'  => 'Verb must be provided',
            '*.object.required'  => 'Object must be provided',
            '*.context.required'  => 'Context must be provided',
        ];
    }
}

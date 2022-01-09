<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpsertStateRequest extends FormRequest
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
            'state_id' => [
                'string',
                'required'
            ],
            'activity_id' => [
                'string',
                'required'
            ],
            'activity_name' => [
                'string',
                'required'
            ],
            'email' => [
                'string',
                'required'
            ],
            'registration' => [
                'string',
                'required'
            ],
            'payload' => [
                'array',
                'required'
            ],
        ];
    }
}

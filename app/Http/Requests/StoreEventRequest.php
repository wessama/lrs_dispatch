<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEventRequest extends FormRequest
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
            'student_uid' => [
                'string',
                'required'
            ],
            'student_email' => [
                'string',
                'required'
            ],
            'student_firstname' => [
                'string',
                'nullable'
            ],
            'student_lastname' => [
                'string',
                'nullable'
            ],
            'activity' => [
                'string',
                'required'
            ],
            'activity_url' => [
                'string',
                'required'
            ],
            'domoscio_content_id' => [
                'integer',
                'required'
            ],
        ];
    }
}

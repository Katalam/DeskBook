<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => 'required|in:empty,more_than,less_than',
            'channel' => 'required|in:email,slack',
            'number' => 'required_if:type,more_than,less_than|numeric',
            'receiver' => 'required|string',
            'message' => 'required|string',
            'rooms' => 'required|array',
            'rooms.*' => 'required|exists:rooms,id',
        ];
    }
}

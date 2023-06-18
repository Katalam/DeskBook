<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:tables',
            'location' => 'sometimes|string|nullable',
            'room_id' => 'required|integer|exists:rooms,id',
            'multiple_bookings' => 'sometimes|boolean',
            'time_off_type_id' => 'sometimes|integer|exists:time_off_types,id|nullable',
            'feature_ids' => 'sometimes|array',
            'feature_ids.*' => 'integer|exists:features,id',
        ];
    }
}

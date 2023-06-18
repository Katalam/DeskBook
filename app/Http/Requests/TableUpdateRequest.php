<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->table);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string',
            'room_id' => 'sometimes|integer|exists:rooms,id',
            'multiple_bookings' => 'sometimes|boolean',
            'time_off_type_id' => 'sometimes|integer|exists:time_off_types,id|nullable',
        ];
    }
}

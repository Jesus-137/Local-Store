<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contact_id' => 'required|exists:contacts,id',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'start' => 'required|date_format:Y-m-d H:i:s|after:now',
            'end' => 'required|date_format:Y-m-d H:i:s|after:start',
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ];
    }
}

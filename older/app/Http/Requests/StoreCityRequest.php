<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'country_id' => ['required'],
            'state_id' => ['required'],
            'name' => ['required', 'string', 'unique:states,name', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}

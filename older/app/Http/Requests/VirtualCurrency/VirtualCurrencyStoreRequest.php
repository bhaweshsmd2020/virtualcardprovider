<?php

namespace App\Http\Requests\VirtualCurrency;

use Illuminate\Foundation\Http\FormRequest;

class VirtualCurrencyStoreRequest extends FormRequest
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
            'preview' => 'required|image',
            'currency' => 'required|string',
            'is_default' => 'required|boolean',
            'status' => ['required', 'string', 'in:active,inactive'],
            'precision' => 'required|integer|min:2',
        ];
    }
}

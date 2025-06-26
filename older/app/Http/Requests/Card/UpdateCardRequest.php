<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:1', 'max:1000'],
            'preview' => request()->hasFile('preview') ? 'image' : 'nullable|string',
            'type' => ['nullable', 'string', 'min:1', 'max:255'],
            'category_type' => ['nullable', 'string', 'min:1'],
            'card_variant' => ['required', 'string', 'min:1', 'max:255'],
            'min_deposit' => 'required|numeric',
            'max_deposit' => 'required|numeric',
            'card_fee' => 'required|numeric',
            'increase_limit_fee' => 'required|numeric',
            'topup_limit' => 'required|numeric',
            'spending_limit' => 'required|numeric',
            'card_limit' => 'required|numeric',
            'required_balance' => 'required|numeric',
            'categories' => ['nullable', 'array', 'min:1'],
            'is_featured' => ['required', 'boolean'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }
}

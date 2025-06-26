<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'username' => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'email' => ['required', 'email', 'max:50'],
            'phone' => ['nullable', 'string', 'max:16'],
            'dob_day' => ['nullable', 'integer'],
            'dob_month' => ['nullable', 'integer'],
            'dob_year' => ['nullable', 'integer'],
            'country_code' => ['nullable', 'string'],
            'country_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'address_line' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:4'],
            'balance' => ['nullable', 'numeric'],
            'status' => ['required', 'boolean'],
            'email_verified_at' => ['required', 'boolean'],
            'phone_verified_at' => ['required', 'boolean'],
            'kyc_verified_at' => ['required', 'boolean'],
            'google2fa_secret' => ['required', 'boolean'],
            'wallets' => ['nullable', 'array'],
        ];
    }
}

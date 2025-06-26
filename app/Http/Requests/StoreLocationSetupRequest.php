<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationSetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation()
    {
        $this->merge([
            'full_name' => $this->first_name . ' ' . $this->last_name,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'full_name' => ['required', 'string'],
            // 'first_name' => ['required', 'string'],
            // 'last_name' => ['required', 'string'],
            'dob_day' => ['required', 'numeric', 'min:1', 'max:31'],
            'dob_month' => ['required', 'numeric', 'min:1', 'max:12'],
            'dob_year' => ['required', 'numeric', 'min:1900', 'max:' . date('Y')],

            'fund_source' => ['nullable', 'string'],
            'employment_status' => ['nullable', 'string'],
            'ssn_ein' => ['nullable'],

            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['nullable'],
            'city_id' => ['nullable'],
            'postal_code' => ['required'],
            'address_line' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string'],
            'phone' => ['required', 'numeric', 'digits_between:9,16'],
            'partner_id' => ['nullable'],
            'refer_by' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'dob_day' => 'day',
            'dob_month' => 'month',
            'dob_year' => 'year',

            'fund_source' => 'fund_source',
            'employment_status' => 'employment_status',
            'ssn_ein' => 'ssn_ein',

            'address_line' => 'address',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
        ];
    }
}

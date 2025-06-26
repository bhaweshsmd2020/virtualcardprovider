<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
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
            'title' => 'required|max:255',
            'description' => 'required',
            'preview' => 'nullable|image|max:1024',
            'days' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'plan_data.cards.value' => 'required|integer|min:1',
            'plan_data.cards.overview' => 'nullable|string',
            'plan_data.deposit_fee.value' => 'required|numeric|between:0,99999999.999999999999',
            'plan_data.deposit_fee.overview' => 'nullable|string',
            'plan_data.transaction_fee.value' => 'required|numeric|between:0,99999999.999999999999',
            'plan_data.transaction_fee.overview' => 'nullable|string',
            'plan_data.service_fee.value' => 'required|numeric|between:0,99999999.999999999999',
            'plan_data.service_fee.overview' => 'nullable|string',
            'is_featured' => 'required|boolean',
            'is_recommended' => 'required|boolean',
            'is_trial' => 'required|boolean',
            'is_default' => 'required|boolean',
            'status' => 'required|boolean',
            'trial_days' => 'required_if:is_trial,true|integer|min:0',
            'extra_data' => 'nullable|array',
        ];
    }

    public function attributes()
    {
        return [
            'plan_data.cards.value' => 'Cards',
            'plan_data.cards.overview' => 'Cards Overview',
            'plan_data.deposit_fee.value' => 'Deposit Fee',
            'plan_data.deposit_fee.overview' => 'Deposit Fee Overview',
            'plan_data.transaction_fee.value' => 'transaction Fee',
            'plan_data.transaction_fee.overview' => 'transaction Fee Overview',
            'plan_data.service_fee.value' => 'Service Fee',
            'plan_data.service_fee.overview' => 'Service Fee Overview',
            'is_featured' => 'Is Featured',
            'is_recommended' => 'Is Recommended',
            'is_trial' => 'Is Trial',
            'is_default' => 'Default Plan',
            'status' => 'Status',
            'trial_days' => 'Trial Days',
            'extra_data.*.key' => 'Extra Perks Key',
            'extra_data.*.value' => 'Extra Perks Value',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'regex:/^(\\+971|00971|0)?(5[024568]\\d{7}|[2-9]\\d{7})$/'],
            'emirate' => ['required', Rule::in(array_keys(config('store.emirates')))],
            'city' => ['required', 'string', 'max:120'],
            'street_address' => ['required', 'string', 'max:220'],
            'building' => ['required', 'string', 'max:120'],
            'apartment' => ['nullable', 'string', 'max:120'],
            'delivery_notes' => ['nullable', 'string', 'max:500'],
            'company_trn' => ['nullable', 'string', 'max:32'],
            'payment_method' => ['required', Rule::in(['card', 'cod'])],
            'delivery_slot_id' => ['nullable', 'exists:delivery_slots,id'],
            'billing_same_as_shipping' => ['nullable', 'boolean'],
            'save_address' => ['nullable', 'boolean'],
        ];
    }
}

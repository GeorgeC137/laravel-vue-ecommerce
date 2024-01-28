<?php

namespace App\Http\Requests;

use App\Enums\CustomerStatus;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'phone' => ['required', 'min:7'],
            'email' => ['required', 'email'],
            'status' => ['required', 'boolean'],

            'shippingAddress.address1' => ['required'],
            'shippingAddress.address2' => ['required'],
            'shippingAddress.city' => ['required'],
            'shippingAddress.state' => ['required'],
            'shippingAddress.zip_code' => ['required'],
            'shippingAddress.country_code' => ['required', 'exists:countries,code'],

            'billingAddress.address1' => ['required'],
            'billingAddress.address2' => ['required'],
            'billingAddress.city' => ['required'],
            'billingAddress.state' => ['required'],
            'billingAddress.zip_code' => ['required'],
            'billingAddress.country_code' => ['required', 'exists:countries,code'],

        ];
    }

    public function attributes()
    {
        return [
            'billingAddress.address1' => 'address 1',
            'billingAddress.address2' => 'address 2',
            'billingAddress.city' => 'city',
            'billingAddress.state' => 'state',
            'billingAddress.zip_code' => 'zip code',
            'billingAddress.country' => 'country',
            'shippingAddress.address1' => 'address 1',
            'shippingAddress.address2' => 'address 2',
            'shippingAddress.city' => 'city',
            'shippingAddress.state' => 'state',
            'shippingAddress.zip_code' => 'zip code',
            'shippingAddress.country_code' => 'country',
        ];
    }
}

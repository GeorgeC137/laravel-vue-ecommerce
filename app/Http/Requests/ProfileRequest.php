<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|min:7',

            'shipping.address1' => 'required',
            'shipping.address2' => 'required',
            'shipping.city' => 'required',
            'shipping.state' => 'required',
            'shipping.zip_code' => 'required',
            'shipping.country_code' => 'required|exists:countries,code',

            'billing.address1' => 'required',
            'billing.address2' => 'required',
            'billing.city' => 'required',
            'billing.state' => 'required',
            'billing.zip_code' => 'required',
            'billing.country_code' => 'required|exists:countries,code',
        ];
    }

    public function attributes()
    {
        return [
            'shipping.address1' => 'address 1',
            'shipping.address2' => 'address 2',
            'shipping.city' => 'city',
            'shipping.state' => 'state',
            'shipping.zip_code' => 'zip code',
            'shipping.country_code' => 'country',

            'billing.address1' => 'address 1',
            'billing.address2' => 'address 2',
            'billing.city' => 'city',
            'billing.state' => 'state',
            'billing.zip_code' => 'zip code',
            'billing.country_code' => 'country',
        ];
    }
}

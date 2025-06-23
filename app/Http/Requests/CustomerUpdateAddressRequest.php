<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         $address = $this->route('address');
        return Auth::check() && $address->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'address_type' => 'required|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ];
    }

     /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'address_type.required' => 'Please select an address type.',
            'address_type.in' => 'Please select a valid address type.',
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'address_line1.required' => 'The address line 1 field is required.',
            'city.required' => 'The city field is required.',
            'state.required' => 'The state field is required.',
            'postal_code.required' => 'The postal code field is required.',
            'country.required' => 'The country field is required.',
        ];
    }
}

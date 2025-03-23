<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'vendor';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'store_name' => 'required|string|max:255',
            'store_phone' => 'required|string|max:20',
            'store_email' => 'required|email|max:255',
            'store_address' => 'required|string',
            'store_city' => 'required|string|max:100',
            'store_state' => 'required|string|max:100',
            'store_postal_code' => 'required|string|max:20',
            'store_country' => 'required|string|max:100',
            'store_description' => 'required|string',
            'store_url' => 'nullable|url|max:255',
            'tax_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_routing_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'store_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'store_name.required' => 'Store name is required',
            'store_phone.required' => 'Store phone number is required',
            'store_email.required' => 'Store email is required',
            'store_email.email' => 'Please provide a valid email address',
            'store_address.required' => 'Store address is required',
            'store_city.required' => 'City is required',
            'store_state.required' => 'State/Province is required',
            'store_postal_code.required' => 'Postal/ZIP code is required',
            'store_country.required' => 'Country is required',
            'store_description.required' => 'Store description is required',
            'store_url.url' => 'Please provide a valid URL',
            'social_facebook.url' => 'Please provide a valid Facebook URL',
            'social_twitter.url' => 'Please provide a valid Twitter URL',
            'social_instagram.url' => 'Please provide a valid Instagram URL',
            'social_youtube.url' => 'Please provide a valid YouTube URL',
            'store_logo.image' => 'Logo must be an image file',
            'store_logo.max' => 'Logo file size should not exceed 2MB',
            'store_banner.image' => 'Banner must be an image file',
            'store_banner.max' => 'Banner file size should not exceed 2MB',
        ];
    }
}

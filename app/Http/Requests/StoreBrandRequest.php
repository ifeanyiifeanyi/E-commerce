<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
                'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
                'description' => ['nullable', 'string'],
                'logo' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg',
                    'max:2048',
                ],
                'website' => ['nullable', 'url', 'max:255'],
                'status' => ['boolean'],
                'is_featured' => ['boolean'],
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string'],
                'meta_keywords' => ['nullable', 'string'],
            ];
    }
}

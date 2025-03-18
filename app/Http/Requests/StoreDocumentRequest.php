<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->user() && request()->user()->isVendor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'document_type' => 'required|string',
            'document_number' => 'nullable|string|max:255',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'expiry_date' => 'nullable|date|after:today',
        ];
    }

    public function attributes(): array
    {
        return [
            'document_type' => 'document type',
            'document_number' => 'document number',
            'document_file' => 'document file',
            'expiry_date' => 'expiry date',
        ];
    }

    public function messages(): array
    {
        return [
            'document_file.max' => 'The document file must not be larger than 10MB.',
            'expiry_date.after' => 'The expiry date must be a future date.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\AdvertisementPackage;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdvertisementPackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', AdvertisementPackage::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:advertisement_packages,name'
            ],
             'description' => 'nullable|string|max:1000',
            'location' => [
                'required',
                'string',
                Rule::in(['home_banner', 'home_sidebar', 'category_top', 'product_detail', 'search_results'])
            ],
             'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999.99'
            ],
             'duration_days' => [
                'required',
                'integer',
                'min:1',
                'max:365'
            ],
            'max_slots' => [
                'required',
                'integer',
                'min:1',
                'max:100'
            ],
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'boolean',
            'notify_vendors' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Package name is required.',
            'name.unique' => 'A package with this name already exists.',
            'location.required' => 'Please select an advertisement location.',
            'location.in' => 'Invalid advertisement location selected.',
            'price.required' => 'Package price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'price.max' => 'Price cannot exceed $99,999.99.',
            'duration_days.required' => 'Duration is required.',
            'duration_days.min' => 'Duration must be at least 1 day.',
            'duration_days.max' => 'Duration cannot exceed 365 days.',
            'max_slots.required' => 'Maximum slots is required.',
            'max_slots.min' => 'At least 1 slot is required.',
            'max_slots.max' => 'Maximum slots cannot exceed 100.',
            'features.*.max' => 'Each feature cannot exceed 255 characters.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'duration_days' => 'duration',
            'max_slots' => 'maximum slots',
            'sort_order' => 'sort order'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up features array
        if ($this->has('features')) {
            $features = array_filter($this->features ?: [], function ($feature) {
                return !empty(trim($feature));
            });
            $this->merge(['features' => array_values($features)]);
        }

        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'notify_vendors' => $this->boolean('notify_vendors', false),
            'sort_order' => $this->input('sort_order', 0)
        ]);
    }


     /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation for location + max_slots combination
            $location = $this->input('location');
            $maxSlots = $this->input('max_slots');

            // Certain locations should have reasonable slot limits
            $locationLimits = [
                'home_banner' => 3,
                'home_sidebar' => 2,
                'category_top' => 5,
                'product_detail' => 10,
                'search_results' => 8
            ];

            if (isset($locationLimits[$location]) && $maxSlots > $locationLimits[$location]) {
                $validator->errors()->add(
                    'max_slots',
                    "For {$location} location, maximum recommended slots is {$locationLimits[$location]}."
                );
            }

            // Validate feature count
            $features = $this->input('features', []);
            if (count($features) > 10) {
                $validator->errors()->add('features', 'Cannot have more than 10 features per package.');
            }

            // Business logic validation
            if ($this->input('price') == 0 && $this->input('duration_days') > 7) {
                $validator->errors()->add('duration_days', 'Free packages cannot run for more than 7 days.');
            }
        });
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'product_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($this->product),
            ],
            'product_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products')->ignore($this->product),
            ],
            'product_qty' => 'required|integer|min:0',
            'product_tags' => 'nullable|string',
            'product_size' => 'nullable|string',
            'product_color' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'product_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'multi_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'hot_deals' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'special_offer' => 'nullable|boolean',
            'special_deals' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ];
    }
}

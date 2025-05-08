<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role == 'vendor');
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
            'product_name' => 'required|string|max:255|unique:products',
            'product_qty' => 'required|integer|min:0',
            'product_tags' => 'nullable|string',
            'product_size' => 'nullable|string',
            'product_color' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'product_thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'multi_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'hot_deals' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'special_offer' => 'nullable|boolean',
            'special_deals' => 'nullable|boolean',
            'status' => 'nullable|boolean',

            // Measurement related fields
            'measurement_unit_id' => 'nullable|exists:measurement_units,id',
            'conversion_factor' => 'nullable|numeric|min:0',
            'is_weight_based' => 'nullable|boolean',
            'allow_decimal_qty' => 'nullable|boolean',
            'min_order_qty' => 'nullable|numeric|min:0.01',
            'max_order_qty' => 'nullable|numeric|min:0.01|gt:min_order_qty',
            'base_unit' => 'nullable|required_if:exi|string|max:255',
            'base_unit' => 'nullable|required_if:is_weight_based,true|string|max:255',
            'base_unit' => 'nullable|required_if:allow_decimal_qty,true|string|max:255',
            'base_unit' => 'nullable|required_if:allow_decimal_qty,false|string|max:255',

            'track_inventory' => 'nullable|boolean',
            'allow_backorders' => 'nullable|boolean',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'enable_stock_alerts' => 'nullable|boolean',
            
        ];
    }

     /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'brand_id' => 'brand',
            'category_id' => 'category',
            'subcategory_id' => 'subcategory',
            'product_qty' => 'product quantity',
            'measurement_unit_id' => 'measurement unit',
            'min_order_qty' => 'minimum order quantity',
            'max_order_qty' => 'maximum order quantity',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'discount_price.lt' => 'The discount price must be less than the selling price.',
            'max_order_qty.gt' => 'The maximum order quantity must be greater than the minimum order quantity.',
        ];
    }
}

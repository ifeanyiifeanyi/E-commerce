<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'vendor');
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
             // Measurement related fields
             'measurement_unit_id' => 'nullable|exists:measurement_units,id',
             'conversion_factor' => 'nullable|numeric|min:0',
             'is_weight_based' => 'nullable|boolean',
             'allow_decimal_qty' => 'nullable|boolean',
             'min_order_qty' => 'nullable|numeric|min:0.01',
             'max_order_qty' => 'nullable|numeric|min:0.01|gt:min_order_qty',

             'track_inventory' => 'nullable|boolean',
             'allow_backorders' => 'nullable|boolean',
             'low_stock_threshold' => 'nullable|integer|min:0',
             'enable_stock_alerts' => 'nullable|boolean',
        ];
    }

     // Reuse the same attributes and messages from ProductStoreRequest
     public function attributes(): array
     {
         return (new ProductStoreRequest())->attributes();
     }

     public function messages(): array
     {
         return (new ProductStoreRequest())->messages();
     }
}

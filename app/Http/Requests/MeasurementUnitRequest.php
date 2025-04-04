<?php

namespace App\Http\Requests;

use App\Models\MeasurementUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class MeasurementUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'type' => 'required|string|in:' . implode(',', array_keys(MeasurementUnit::getTypes())),
            'is_base_unit' => 'boolean',
            'base_unit_id' => 'nullable|required_if:is_base_unit,0|exists:measurement_units,id',
            'conversion_factor' => 'nullable|required_if:is_base_unit,0|numeric|min:0.000001',
            'is_active' => 'boolean',
        ];
    }

     /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'is_base_unit' => $this->boolean('is_base_unit'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

     /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'base_unit_id.required_if' => 'The base unit field is required when this is not a base unit.',
            'conversion_factor.required_if' => 'The conversion factor field is required when this is not a base unit.',
        ];
    }
}

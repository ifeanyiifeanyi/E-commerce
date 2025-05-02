<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
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
            'quantity_change' => 'required|numeric',
            'action_type' => 'required|in:purchase,adjustment,return,count,damage,sale',
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity_change.required' => 'Please enter a quantity change value.',
            'quantity_change.numeric' => 'Quantity must be a number.',
            'action_type.required' => 'Please select an action type.',
            'action_type.in' => 'Selected action type is invalid.',
        ];
    }
}

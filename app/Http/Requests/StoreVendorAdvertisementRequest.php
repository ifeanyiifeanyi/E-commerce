<?php

namespace App\Http\Requests;

use App\Models\AdvertisementPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\AdminAdvertisementPackageService;

class StoreVendorAdvertisementRequest extends FormRequest
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
    public function rules(): array
    {

        $packageService = app(AdminAdvertisementPackageService::class);
        $locations = $packageService->getAvailableLocations();
        $package = AdvertisementPackage::find($this->package_id);
        // dd($locations);

        if (!$package) {
            return [];
        }

        $location = $locations[$package->location] ?? null;
        return [
            'package_id' => 'required|exists:advertisement_packages,id',
            'product_id' => 'nullable|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'link_url' => 'nullable|url|max:255',
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:' . ($location['max_file_size'] ?? 2048),
                function ($attribute, $value, $fail) use ($location) {
                    if ($location) {
                        $image = getimagesize($value);
                        $width = $image[0];
                        $height = $image[1];
                        if ($width != $location['dimensions']['width'] || $height != $location['dimensions']['height']) {
                            $fail("The image dimensions must be exactly {$location['recommended_size']}.");
                        }
                    }
                },
            ],
            'auto_renew' => 'boolean',
        ];
    }


    public function messages()
    {
        return [
            'image.dimensions' => 'The image must match the required dimensions for the selected package location.',
            'image.max' => 'The image file size must not exceed the maximum allowed size for this package.',
        ];
    }
}

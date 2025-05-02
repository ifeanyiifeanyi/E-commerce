@component('mail::message')
# Product Restocked Alert

**Product: {{ $product->product_name }}**

Good news! This product is now back in stock. Current details:

- **Product Code:** {{ $product->product_code }}
- **Current Stock:** {{ $product->formattedQuantity() }}
- **Category:** {{ $product->category ? $product->category->name : 'N/A' }}
- **Brand:** {{ $product->brand ? $product->brand->name : 'N/A' }}

@component('mail::button', ['url' => route('admin.inventory.show', $product->id)])
View Product
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

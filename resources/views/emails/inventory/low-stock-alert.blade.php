@component('mail::message')
# Low Stock Alert

**Product: {{ $product->product_name }}**

This product has fallen below its low stock threshold. Current details:

- **Product Code:** {{ $product->product_code }}
- **Current Stock:** {{ $product->formattedQuantity() }}
- **Low Stock Threshold:** {{ $product->low_stock_threshold }} {{ $product->formattedMeasure }}
- **Category:** {{ $product->category ? $product->category->name : 'N/A' }}
- **Brand:** {{ $product->brand ? $product->brand->name : 'N/A' }}

@component('mail::button', ['url' => route('admin.inventory.show', $product->id)])
View Product
@endcomponent

Please consider restocking this product soon.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

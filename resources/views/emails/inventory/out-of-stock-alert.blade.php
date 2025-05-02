@component('mail::message')
# Out of Stock Alert

**Product: {{ $product->product_name }}**

This product is now out of stock. Current details:

- **Product Code:** {{ $product->product_code }}
- **Current Stock:** {{ $product->formattedQuantity() }}
- **Category:** {{ $product->category ? $product->category->name : 'N/A' }}
- **Brand:** {{ $product->brand ? $product->brand->name : 'N/A' }}
- **Backorders Allowed:** {{ $product->allow_backorders ? 'Yes' : 'No' }}

@if($product->allow_backorders)
Customers can still place orders for this product as backorders are enabled.
@else
Customers cannot place orders for this product until it is restocked.
@endif

@component('mail::button', ['url' => route('admin.inventory.show', $product->id)])
View Product
@endcomponent

Please restock this product as soon as possible.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

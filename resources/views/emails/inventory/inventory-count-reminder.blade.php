@component('mail::message')
# Inventory Count Reminder

The following products are due for an inventory count:

@component('mail::table')
| Product | Code | Last Count | Current Stock |
|---------|------|------------|---------------|
@foreach($products as $product)
| {{ $product->product_name }} | {{ $product->product_code }} | {{ $product->stock_last_updated ? $product->stock_last_updated->format('Y-m-d') : 'Never' }} | {{ $product->formattedQuantity() }} |
@endforeach
@endcomponent

Please schedule an inventory count for these products soon to ensure accurate stock levels.

@component('mail::button', ['url' => route('admin.inventory')])
View Inventory
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

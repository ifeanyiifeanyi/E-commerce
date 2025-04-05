<?php

namespace App\Observers;

use App\Models\Product;
use Spatie\Activitylog\Facades\LogActivity;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        activity()
            ->performedOn($product)
            ->withProperties([
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'category' => $product->category->name ?? 'Unknown Category',
                'quantity' => $product->formattedQuantity(),
                'price' => $product->selling_price
            ])
            ->log('Product created');
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        $changes = $product->getChanges();
        $original = $product->getOriginal();

        // Skip logging if only timestamps were updated
        if (count($changes) <= 2 && isset($changes['updated_at'])) {
            return;
        }

        $changedFields = [];
        foreach ($changes as $field => $newValue) {
            if ($field !== 'updated_at' && $field !== 'created_at') {
                $changedFields[$field] = [
                    'old' => $original[$field] ?? null,
                    'new' => $newValue
                ];
            }
        }

        activity()
            ->performedOn($product)
            ->withProperties([
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'changes' => $changedFields
            ])
            ->log('Product updated');
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        activity()
            ->performedOn($product)
            ->withProperties([
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_code' => $product->product_code
            ])
            ->log('Product deleted');
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        activity()
            ->performedOn($product)
            ->withProperties([
                'product_id' => $product->id,
                'product_name' => $product->product_name
            ])
            ->log('Product restored');
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        activity()
            ->performedOn($product)
            ->withProperties([
                'product_id' => $product->id,
                'product_name' => $product->product_name
            ])
            ->log('Product permanently deleted');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMultiImage extends Model
{
    protected $fillable = [
        'product_id',
        'photo_name',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

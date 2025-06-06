<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdvertisementAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_id',
        'date',
        'impressions',
        'clicks',
        'ctr',
    ];

    protected $casts = [
        'date' => 'date',
        'ctr' => 'decimal:2',
    ];

    public function advertisement()
    {
        return $this->belongsTo(VendorAdvertisement::class, 'advertisement_id');
    }
}

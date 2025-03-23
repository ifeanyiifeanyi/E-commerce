<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorStore extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'store_phone',
        'store_address',
        'store_city',
        'store_state',
        'store_postal_code',
        'store_country',
        'store_email',
        'store_logo',
        'store_banner',
        'store_description',
        'store_url',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'social_youtube',
        'commission_rate',
        'tax_number',
        'bank_name',
        'bank_account_number',
        'bank_routing_number',
        'bank_account_name',
        'is_featured',
        'status',
        'rejection_reason',
        'join_date',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];


    protected $casts = [
        'join_date' => 'datetime',
        'is_featured' => 'boolean',
        'status' => 'string',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasOneThrough(
            VendorDocument::class,
            User::class,
            'id', // Foreign key on the User table
            'user_id', // Foreign key on the VendorDocument table
            'user_id', // Local key on the VendorStore table
            'id' // Local key on the User table
        );
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id', 'user_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class, 'vendor_id', 'user_id');
    // }

    // public function getAverageRatingAttribute()
    // {
    //     return $this->reviews()->avg('rating') ?: 0;
    // }
}

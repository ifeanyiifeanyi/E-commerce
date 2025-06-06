<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementNotification extends Model
{
     protected $fillable = [
        'advertisement_id',
        'vendor_id',
        'type',
        'message',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function advertisement()
    {
        return $this->belongsTo(VendorAdvertisement::class, 'advertisement_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'expiry_warning' => 'Expiry Warning',
            'expired' => 'Advertisement Expired',
            'payment_reminder' => 'Payment Reminder',
            'approved' => 'Advertisement Approved',
            'rejected' => 'Advertisement Rejected',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }
}

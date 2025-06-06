<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Models\VendorAdvertisement;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdvertisementPayment extends Model
{
     use HasFactory, LogsActivity;

    protected $fillable = [
        'advertisement_id',
        'vendor_id',
        'payment_reference',
        'amount',
        'payment_method',
        'payment_status',
        'payment_data',
        'payment_date',
        'notes',
        'refunded_at',
        'refund_reason',
    ];


    protected $casts = [
        'payment_data' => 'array',
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_COMPLETED = 'completed';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    const CARD_PAYMENT = 'card';
    const BANK_TRANSFER = 'bank_transfer';
    const WALLET_PAYMENT = 'wallet';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['payment_status', 'amount', 'payment_method'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function advertisement()
    {
        return $this->belongsTo(VendorAdvertisement::class, 'advertisement_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }

    public function isRefunded()
    {
        return $this->payment_status === 'refunded';
    }

    public function refund(string $reason)
    {
        $this->update([
            'payment_status' => 'refunded',
            'refunded_at' => now(),
            'refund_reason' => $reason
        ]);
    }
}

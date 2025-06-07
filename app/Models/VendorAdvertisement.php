<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Models\AdvertisementAnalytic;
use Illuminate\Database\Eloquent\Model;

class VendorAdvertisement extends Model
{
    protected $fillable = [
        'vendor_id',
        'product_id',
        'package_id',
        'title',
        'description',
        'image_path',
        'link_url',
        'status',
        'amount_paid',
        'start_date',
        'end_date',
        'expires_at',
        'clicks',
        'impressions',
        'auto_renew',
        'admin_notes',
        'rejection_reason',
        'ctr',
        'payment_status',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'ctr' => 'decimal:4',
        'auto_renew' => 'boolean',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REJECTED = 'rejected';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_COMPLETED = 'completed';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'amount_paid'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function notifications()
    {
        return $this->hasMany(AdvertisementNotification::class, 'advertisement_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at > now();
    }



    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function package()
    {
        return $this->belongsTo(AdvertisementPackage::class, 'package_id');
    }

    public function payments()
    {
        return $this->hasMany(AdvertisementPayment::class, 'advertisement_id');
    }

    public function analytics()
    {
        return $this->hasMany(AdvertisementAnalytic::class, 'advertisement_id');
    }



    public function isExpired()
    {
        return $this->expires_at <= now();
    }

    public function isExpiringSoon($days = 7)
    {
        return $this->expires_at <= now()->addDays($days);
    }

    public function getDaysRemainingAttribute()
    {
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getCtrAttribute()
    {
        return $this->impressions > 0 ? round(($this->clicks / $this->impressions) * 100, 2) : 0;
    }

    public function recordImpression()
    {
        $this->increment('impressions');
        $this->updateCtr();

        // Record daily analytics
        $this->recordDailyAnalytic('impression');
    }

    public function recordClick()
    {
        $this->increment('clicks');
        $this->updateCtr();

        // Record daily analytics
        $this->recordDailyAnalytic('click');
    }

    protected function updateCtr()
    {
        if ($this->impressions > 0) {
            $ctr = ($this->clicks / $this->impressions) * 100;
            $this->update(['ctr' => $ctr]);
        }
    }

    protected function recordDailyAnalytic($type)
    {
        $today = now()->format('Y-m-d');

        $analytic = AdvertisementAnalytic::firstOrCreate(
            [
                'advertisement_id' => $this->id,
                'date' => $today,
            ],
            [
                'impressions' => 0,
                'clicks' => 0,
                'ctr' => 0,
            ]
        );

        if ($type === 'impression') {
            $analytic->increment('impressions');
        } elseif ($type === 'click') {
            $analytic->increment('clicks');
        }

        // Update CTR
        if ($analytic->impressions > 0) {
            $ctr = ($analytic->clicks / $analytic->impressions) * 100;
            $analytic->update(['ctr' => $ctr]);
        }
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'active' => '<span class="badge badge-success">Active</span>',
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'rejected' => '<span class="badge badge-danger">Rejected</span>',
            'expired' => '<span class="badge badge-secondary">Expired</span>',
            'paused' => '<span class="badge badge-info">Paused</span>',
            default => '<span class="badge badge-light">' . ucfirst($this->status) . '</span>'
        };
    }

    /**
     * Check if the advertisement can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if status is active and not expired
        if ($this->status === self::STATUS_ACTIVE && !$this->isExpired()) {
            return false;
        }

        // Cannot delete if status is pending (awaiting approval)
        if ($this->status === self::STATUS_PENDING) {
            return false;
        }

        // Can delete if status is paused, rejected, or expired
        return in_array($this->status, [
            self::STATUS_PAUSED,
            self::STATUS_REJECTED,
            self::STATUS_EXPIRED
        ]);
    }

    /**
     * Delete associated payments
     */
    public function deleteAssociatedPayments(): void
    {
        $this->payments()->delete();
    }
}

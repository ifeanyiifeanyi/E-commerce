<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Activity type categories for statistics
     */
    public const ACTIVITY_CATEGORIES = [
        'profile' => [
            'profile_updated',
            'profile_created',
            'profile_photo_updated',
            'profile_settings_changed',
            'marketing_preferences_updated',
        ],
        'security' => [
            'password_changed',
            'security_settings_updated',
            'two_factor_enabled',
            'two_factor_disabled',
            'login_success',
            'login_failed',
            'account_locked',
            'account_unlocked',
            'password_reset_requested',
            'password_reset_completed',
        ],
        'address' => [
            'address_created',
            'address_updated',
            'address_deleted',
            'default_address_changed',
        ],
        'order' => [
            'order_created',
            'order_updated',
            'order_cancelled',
            'order_completed',
            'order_shipped',
            'order_delivered',
            'order_refunded',
            'cart_updated',
            'wishlist_updated',
        ],
        'notification' => [
            'notifications_bulk_read',
            'notification_read',
            'notification_settings_updated',
            'email_preferences_updated',
        ],
        'system' => [
            'system_login',
            'system_logout',
            'user_logout',
            'session_expired',
            'account_verification',
            'email_verified',
        ],
        'promotion' => [
            'promotion_applied',
            'promotion_removed',
            'coupon_used',
            'discount_applied',
            'loyalty_points_earned',
            'loyalty_points_redeemed',
        ],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedModel(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Static method to log activity with enhanced information
     */
    public static function log(
        int $userId,
        string $activityType,
        string $description = null,
        array $properties = null,
        string $ipAddress = null,
        string $userAgent = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'properties' => $properties ?? [],
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Scope for filtering by activity category
     */
    public function scopeByCategory($query, string $category)
    {
        $activityTypes = self::ACTIVITY_CATEGORIES[$category] ?? [];
        return $query->whereIn('activity_type', $activityTypes);
    }

    /**
     * Get activity category
     */
    public function getCategoryAttribute(): ?string
    {
        foreach (self::ACTIVITY_CATEGORIES as $category => $types) {
            if (in_array($this->activity_type, $types)) {
                return $category;
            }
        }
        return 'other';
    }

    /**
     * Get the subject from properties if available
     */
    public function getSubjectAttribute(): ?string
    {
        if (!$this->properties) {
            return null;
        }

        $subjectType = $this->properties['subject_type'] ?? null;
        $subjectId = $this->properties['subject_id'] ?? null;

        if ($subjectType && $subjectId) {
            return "{$subjectType}#{$subjectId}";
        }

        return null;
    }

    /**
     * Get the event from properties
     */
    public function getEventAttribute(): ?string
    {
        return $this->properties['event'] ?? $this->activity_type;
    }

    /**
     * Get formatted IP address
     */
    public function getFormattedIpAttribute(): string
    {
        return $this->ip_address ?? 'Unknown';
    }

    /**
     * Get browser information from user agent
     */
    public function getBrowserInfoAttribute(): string
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        // Simple browser detection
        $userAgent = $this->user_agent;

        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }

    /**
     * Scope for filtering by activity type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for activities by IP address
     */
    public function scopeByIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Get activity statistics by category
     */
    public static function getStatisticsByCategory(int $userId): array
    {
        $activities = static::where('user_id', $userId)->get();
        $stats = [];

        foreach (self::ACTIVITY_CATEGORIES as $category => $types) {
            $stats[$category] = $activities->whereIn('activity_type', $types)->count();
        }

        $stats['total'] = $activities->count();

        return $stats;
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\EnhancedActivityLogging;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, EnhancedActivityLogging;




    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'photo',
        'phone',
        'address',
        'account_type',
        'role',
        'country',
        'status',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_recovery_codes',
        'email_verified_at',

        // New customer fields
        'city',
        'state',
        'postal_code',
        'last_login_at',
        'last_login_ip',
        'device_info',
        'browser_info',
        'os_info',
        'latitude',
        'longitude',
        'registration_source',
        'referral_source',
        'customer_notes',
        'marketing_preferences',
        'last_activity_at',
        'account_status', // active, suspended, banned, etc.
        'customer_segment', // VIP, regular, new, etc.
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'marketing_preferences' => 'array',
        ];
    }



    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getInitialsAttribute(): string
    {
        return substr($this->name, 0, 1);
    }
    public function getUsernameAttribute(): string
    {
        return $this->attributes['username'] ?? '';
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->photo ? asset($this->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }

    // Add this to your User model
    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function products(){
        return $this->hasMany(Product::class, 'vendor_id');
    }

    public function scopeActiveCustomers($query)
    {
        return $query->where('role', 'user')
            ->where('account_status', 'active');
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }
    public function is_customer(): bool
    {
        return $this->role === 'user';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getAdminMemberAttribute()
    {
        return self::where('role', 'admin')->get();
    }

    public static function getAdminMembers()
    {
        return self::where('role', 'admin')->get()->toArray();
    }
    public function hasRole(array $role): bool
    {
        return in_array($this->role, $role);
    }

    /**
     * Get all orders belonging to the customer
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all addresses for the customer
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Get all wishlist items for the customer
     */
    // public function wishlist(): HasMany
    // {
    //     return $this->hasMany(Wishlist::class);
    // }

    /**
     * Get customer login history
     */
    public function loginHistory(): HasMany
    {
        return $this->hasMany(CustomerLoginHistory::class);
    }

    /**
     * Get customer activity logs
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(CustomerActivityLog::class);
    }

    /**
     * Get customer notifications
     */
    public function customerNotifications(): HasMany
    {
        return $this->hasMany(CustomerNotification::class);
    }

    /**
     * Get customer email campaigns
     */
    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(CustomerEmailCampaign::class);
    }

    /**
     * Check if customer is active
     */
    public function isActive(): bool
    {
        return $this->account_status === 'active';
    }

    /**
     * Calculate customer lifetime value
     */
    public function getLifetimeValue()
    {
        return $this->orders()->where('payment_status', 'paid')->sum('total_amount');
    }

    /**
     * Get formatted location
     */
    public function getFormattedLocationAttribute(): string
    {
        $location = [];

        if ($this->city) $location[] = $this->city;
        if ($this->state) $location[] = $this->state;
        if ($this->country) $location[] = $this->country;

        return implode(', ', $location);
    }

    /**
     * Get map URL for customer location
     */
    public function getMapUrlAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        return '#';
    }

    /**
     * Check if customer has completed profile
     */
    public function hasCompleteProfile(): bool
    {
        return $this->name && $this->email && $this->phone && $this->address;
    }

    /**
     * Count days since registration
     */
    public function getDaysSinceRegistrationAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Count days since last order
     */
    public function getDaysSinceLastOrderAttribute(): int
    {
        $lastOrder = $this->orders()->latest()->first();

        if (!$lastOrder) {
            return 0;
        }

        return $lastOrder->created_at->diffInDays(now());
    }

    public static function evented(){
        static::created(function ($user) {
            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->url(),
                    'method' => request()->method(),
                ])
                ->log('User created');
        });

        static::updated(function ($user) {
            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->url(),
                    'method' => request()->method(),
                ])
                ->log('User updated');
        });

        static::deleted(function ($user) {
            activity()
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->url(),
                    'method' => request()->method(),
                ])
                ->log('User deleted');
        });
    }
}

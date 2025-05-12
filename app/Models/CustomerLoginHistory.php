<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerLoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'device_type',
        'device_name',
        'browser',
        'browser_version',
        'operating_system',
        'os_version',
        'user_agent',
        'location_city',
        'location_state',
        'location_country',
        'latitude',
        'longitude',
        'successful',
        'failure_reason',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDeviceInfoAttribute(): string
    {
        return "{$this->device_type} {$this->device_name}";
    }

    public function getBrowserInfoAttribute(): string
    {
        return "{$this->browser} {$this->browser_version}";
    }

    public function getOsInfoAttribute(): string
    {
        return "{$this->operating_system} {$this->os_version}";
    }

    public function getLocationAttribute(): string
    {
        $location = [];

        if ($this->location_city) $location[] = $this->location_city;
        if ($this->location_state) $location[] = $this->location_state;
        if ($this->location_country) $location[] = $this->location_country;

        return implode(', ', $location);
    }
}

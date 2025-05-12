<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'notification_type',
        'status',
        'read_at',
        'link_url',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->status = 'read';
        $this->read_at = now();
        $this->save();

        return $this;
    }

    public function isRead(): bool
    {
        return $this->status === 'read';
    }

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }
}

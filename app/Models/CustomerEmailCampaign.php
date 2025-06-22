<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerEmailCampaign extends Model
{
    // protected $fillable = [
    //     'user_id',
    //     'campaign_id',
    //     'email_type',
    //     'subject',
    //     'content',
    //     'sent_at',
    //     'opened_at',
    //     'clicked_at',
    //     'open_count',
    //     'click_count',
    // ];
    protected $fillable = [
        'user_id',
        'campaign_id',
        'email_type',
        'subject',
        'content',
        'sent_at',
        'opened_at',
        'clicked_at',
        'open_count',
        'click_count',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // look for out for this METHOD USAGE
    // public function campaign(): BelongsTo
    // {
    //     return $this->belongsTo(CustomerEmailCampaign::class);
    // }

    public function markAsOpened()
    {
        $this->opened_at = $this->opened_at ?? now();
        $this->open_count++;
        $this->save();

        return $this;
    }

    public function markAsClicked()
    {
        $this->clicked_at = $this->clicked_at ?? now();
        $this->click_count++;
        $this->save();

        return $this;
    }

    public function isOpened(): bool
    {
        return $this->opened_at !== null;
    }

    public function isClicked(): bool
    {
        return $this->clicked_at !== null;
    }
}

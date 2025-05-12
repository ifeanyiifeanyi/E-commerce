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
        'ip_address',
        'user_agent',
        'related_model_type',
        'related_model_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedModel(): MorphTo
    {
        return $this->morphTo();
    }

    public static function log($userId, $activityType, $description = null, $relatedModel = null, $ipAddress = null)
    {
        $data = [
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($relatedModel) {
            $data['related_model_type'] = get_class($relatedModel);
            $data['related_model_id'] = $relatedModel->id;
        }

        return self::create($data);
    }
}

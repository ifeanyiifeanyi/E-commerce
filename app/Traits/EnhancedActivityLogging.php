<?php

namespace App\Traits;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\LogsActivity as LogActivity;
trait EnhancedActivityLogging
{
    use LogActivity;
      /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => $this->getActivityDescription($eventName))
            ->useLogName('user_activities');
    }
     /**
     * Get custom description for different events
     */
    protected function getActivityDescription(string $eventName): string
    {
        $modelName = class_basename($this);

        return match($eventName) {
            'created' => "{$modelName} created",
            'updated' => "{$modelName} updated",
            'deleted' => "{$modelName} deleted",
            default => "{$modelName} {$eventName}",
        };
    }

     /**
     * Boot the trait
     */
    protected static function bootEnhancedActivityLogging()
    {
        static::evented(function () {
            // Add IP address and user agent to all activities
            if (request()) {
                activity()
                    ->withProperties([
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'url' => request()->url(),
                        'method' => request()->method(),
                    ]);
            }
        });
    }
}

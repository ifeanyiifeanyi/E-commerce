<?php

namespace App\Observers;

use App\Models\MeasurementUnit;
use Spatie\Activitylog\Facades\LogBatch;

class MeasurementUnitObserver
{
    /**
     * Handle the MeasurementUnit "created" event.
     *
     * @param  \App\Models\MeasurementUnit  $measurementUnit
     * @return void
     */
    public function created(MeasurementUnit $measurementUnit)
    {
        activity()
            ->performedOn($measurementUnit)
            ->withProperties([
                'name' => $measurementUnit->name,
                'symbol' => $measurementUnit->symbol,
                'type' => $measurementUnit->type
            ])
            ->log('Measurement unit created');
    }

    /**
     * Handle the MeasurementUnit "updated" event.
     *
     * @param  \App\Models\MeasurementUnit  $measurementUnit
     * @return void
     */
    public function updated(MeasurementUnit $measurementUnit)
    {
        $changes = $measurementUnit->getChanges();
        $original = $measurementUnit->getOriginal();

        // Only log if there are actual changes
        if (count($changes) > 0) {
            activity()
                ->performedOn($measurementUnit)
                ->withProperties([
                    'changes' => $changes,
                    'original' => $original
                ])
                ->log('Measurement unit updated');
        }
    }

    /**
     * Handle the MeasurementUnit "deleted" event.
     *
     * @param  \App\Models\MeasurementUnit  $measurementUnit
     * @return void
     */
    public function deleted(MeasurementUnit $measurementUnit)
    {
        activity()
            ->performedOn($measurementUnit)
            ->withProperties([
                'name' => $measurementUnit->name,
                'symbol' => $measurementUnit->symbol,
                'type' => $measurementUnit->type,
                'id' => $measurementUnit->id
            ])
            ->log('Measurement unit deleted');
    }

    /**
     * Handle the MeasurementUnit "restored" event.
     *
     * @param  \App\Models\MeasurementUnit  $measurementUnit
     * @return void
     */
    public function restored(MeasurementUnit $measurementUnit)
    {
        activity()
            ->performedOn($measurementUnit)
            ->withProperties([
                'name' => $measurementUnit->name,
                'symbol' => $measurementUnit->symbol,
                'type' => $measurementUnit->type
            ])
            ->log('Measurement unit restored');
    }

    /**
     * Handle the MeasurementUnit "force deleted" event.
     *
     * @param  \App\Models\MeasurementUnit  $measurementUnit
     * @return void
     */
    public function forceDeleted(MeasurementUnit $measurementUnit)
    {
        activity()
            ->performedOn($measurementUnit)
            ->withProperties([
                'name' => $measurementUnit->name,
                'symbol' => $measurementUnit->symbol,
                'type' => $measurementUnit->type,
                'id' => $measurementUnit->id
            ])
            ->log('Measurement unit force deleted');
    }
}

<?php

namespace App\Jobs;

use App\Services\InventoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckInventoryStatuses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService): void
    {
        try {
            $updatedCount = $inventoryService->checkInventoryStatuses();
            Log::info("Updated status for {$updatedCount} products");
        } catch (\Exception $e) {
            Log::error('Error checking inventory statuses: ' . $e->getMessage());
        }
    }
}

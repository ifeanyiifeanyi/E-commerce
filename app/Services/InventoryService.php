<?php

namespace App\Services;

use App\Models\Product;
use App\Mail\RestockAlert;
use App\Mail\LowStockAlert;
use App\Mail\OutOfStockAlert;
use App\Models\InventoryAlert;
use App\Models\ScheduledCount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendInventoryCountReminder;
use App\Models\User;

class InventoryService
{
    /**
     * Process all unresolved inventory alerts.
     */
    public function processInventoryAlerts()
    {
        // Find all unresolved alerts
        $alerts = InventoryAlert::with('product')
            ->where('is_resolved', false)
            ->get();

        foreach ($alerts as $alert) {
            $this->processAlert($alert);
        }

        return $alerts->count();
    }
    /**
     * Process a specific inventory alert.
     */
    protected function processAlert(InventoryAlert $alert)
    {
        if (!$alert->product) {
            // If the product was deleted, auto-resolve the alert
            $alert->resolve(null, 'Auto-resolved because product no longer exists');
            return;
        }

                // Get admin emails
                $adminEmails = User::getAdminMembers();

        if (empty($adminEmails)) {
            Log::warning('No admin emails configured for inventory alerts');
            return;
        }

        try {
            switch ($alert->alert_type) {
                case 'low_stock':
                    Mail::to($adminEmails)->queue(new LowStockAlert($alert->product));
                    break;

                case 'out_of_stock':
                    Mail::to($adminEmails)->queue(new OutOfStockAlert($alert->product));
                    break;

                case 'restock':
                    Mail::to($adminEmails)->queue(new RestockAlert($alert->product));
                    // Auto-resolve restock alerts
                    $alert->resolve(null, 'Auto-resolved when product was restocked');
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send inventory alert email: ' . $e->getMessage());
        }
    }

    /**
     * Schedule inventory count reminder.
     */
    public function scheduleInventoryCountReminder($productIds = null)
    {
        // Get products that need inventory count
        $query = Product::query();

        if ($productIds) {
            $query->whereIn('id', $productIds);
        }

        // Get products that haven't been counted in the last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        $products = $query->where(function ($q) use ($thirtyDaysAgo) {
            $q->where('stock_last_updated', '<', $thirtyDaysAgo)
                ->orWhereNull('stock_last_updated');
        })->get();

        if ($products->isEmpty()) {
            return 0;
        }

        // Get admin emails
        $adminEmails = User::getAdminMembers();

        if (empty($adminEmails)) {
            Log::warning('No admin emails configured for inventory count reminders');
            return 0;
        }

        // Dispatch job to send reminder emails
        dispatch(new SendInventoryCountReminder($products, $adminEmails));

        return $products->count();
    }

      public function checkInventoryStatuses()
    {
        $products = Product::where('track_inventory', true)->get();
        $updatedCount = 0;

        foreach ($products as $product) {
            $oldStatus = $product->stock_status;
            $product->updateStockStatus();

            if ($oldStatus !== $product->stock_status) {
                $updatedCount++;
            }
        }

        return $updatedCount;
    }
     /**
     * Run automated inventory adjustments based on scheduled counts.
     */
    // public function processScheduledCounts()
    // {
    //     // Implementation depends on your specific business logic
    //     // This would typically connect to scheduled inventory counts
    //     // and automatically adjust inventory based on the results

    //     // Example implementation:
    //     $scheduledCounts = ScheduledCount::where('status', 'pending')
    //         ->where('scheduled_date', '<=', now())
    //         ->get();

    //     foreach ($scheduledCounts as $count) {
    //         // Process the count...
    //     }

    //     return true;
    // }

}

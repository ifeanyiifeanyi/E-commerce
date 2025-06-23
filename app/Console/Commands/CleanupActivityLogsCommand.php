<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerActivityLog;
use Spatie\Activitylog\Models\Activity;

class CleanupActivityLogsCommand extends Command
{
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:cleanup {--days=90 : Number of days to keep logs}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old activity logs';

   /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up activity logs older than {$days} days...");

        // Clean up custom activity logs
        $customDeleted = CustomerActivityLog::where('created_at', '<', $cutoffDate)->delete();
        $this->info("Deleted {$customDeleted} custom activity log records");

        // Clean up Spatie activity logs
        $spatieDeleted = Activity::where('created_at', '<', $cutoffDate)->delete();
        $this->info("Deleted {$spatieDeleted} Spatie activity log records");

        $this->info('Activity logs cleanup completed!');

        return Command::SUCCESS;
    }
}

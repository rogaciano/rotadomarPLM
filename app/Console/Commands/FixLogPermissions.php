<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixLogPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:fix-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix permissions for Laravel log files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logPath = storage_path('logs');
        $this->info("Fixing permissions for log files in: {$logPath}");
        
        // Get all log files
        $logFiles = glob("{$logPath}/*.log");
        
        if (empty($logFiles)) {
            $this->info("No log files found.");
            return 0;
        }
        
        $count = 0;
        foreach ($logFiles as $logFile) {
            // Change permissions to 0666 (readable and writable by everyone)
            if (@chmod($logFile, 0666)) {
                $this->info("Fixed permissions for: " . basename($logFile));
                $count++;
            } else {
                $this->error("Failed to fix permissions for: " . basename($logFile));
            }
        }
        
        // Also fix the logs directory itself
        if (@chmod($logPath, 0775)) {
            $this->info("Fixed permissions for logs directory");
        }
        
        $this->info("Completed! Fixed permissions for {$count} log files.");
        
        return 0;
    }
}

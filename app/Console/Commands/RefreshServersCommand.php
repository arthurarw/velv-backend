<?php

namespace App\Console\Commands;

use App\Tasks\RefreshServersAndLocationsTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class RefreshServersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-servers-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh data from spreadsheet every minute';

    /**
     * Execute the console command.
     * @return mixed
     * @throws Exception
     */
    public function handle(): mixed
    {
        try {
            Log::driver('cron-servers')->info('Cron executada às: ' . Carbon::now()->format('d/m/Y H:i:s'));
            return (new RefreshServersAndLocationsTask())->run();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}

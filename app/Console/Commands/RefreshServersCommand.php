<?php

namespace App\Console\Commands;

use App\Tasks\RefreshServersAndLocationsTask;
use Exception;
use Illuminate\Console\Command;

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
            return (new RefreshServersAndLocationsTask())->run();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}

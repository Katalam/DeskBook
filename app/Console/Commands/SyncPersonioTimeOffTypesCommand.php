<?php

namespace App\Console\Commands;

use App\Jobs\SyncPersonioTimeOffTypesJob;
use App\Models\Team;
use Illuminate\Console\Command;

class SyncPersonioTimeOffTypesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-personio-time-off-types-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will create a job for each team to sync the time off types from Personio';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $teams = Team::query()
            ->whereNotNull(['personio_client_id', 'personio_client_secret'])
            ->get();

        $teams->each(function (Team $team) {
            SyncPersonioTimeOffTypesJob::dispatch($team);
        });
    }
}

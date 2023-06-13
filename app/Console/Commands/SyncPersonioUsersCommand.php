<?php

namespace App\Console\Commands;

use App\Jobs\SyncPersonioUsersJob;
use App\Models\Team;
use Illuminate\Console\Command;

class SyncPersonioUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-personio-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will create a job for each team to sync the users from Personio';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $teams = Team::query()
            ->whereNotNull(['personio_client_id', 'personio_client_secret'])
            ->get();

        $teams->each(function (Team $team) {
            SyncPersonioUsersJob::dispatch($team);
        });
    }
}

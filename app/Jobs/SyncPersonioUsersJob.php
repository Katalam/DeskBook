<?php

namespace App\Jobs;

use App\Models\Team;
use App\Services\PersonioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SyncPersonioUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Team $team)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $personioService = new PersonioService($this->team);

        $personioService->syncAllEmployees();
    }
}

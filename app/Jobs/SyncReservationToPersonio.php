<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\PersonioService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class SyncReservationToPersonio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 100;

    public int $maxExceptions = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Reservation $reservation)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->reservation->table?->room?->team) {
            return;
        }

        $personioService = new PersonioService($this->reservation->table->room->team);

        $personioService->syncReservation($this->reservation);
    }

    /**
     * @throws Exception
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('personio:'.$this->reservation->table->room->team->id))
                ->shared()
                ->releaseAfter(10 + random_int(10, 20))
                ->expireAfter(180),
        ];
    }
}

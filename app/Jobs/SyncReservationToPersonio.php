<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\PersonioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class SyncReservationToPersonio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->reservation->table->room->team->id)];
    }
}

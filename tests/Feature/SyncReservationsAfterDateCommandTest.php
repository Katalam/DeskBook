<?php

use App\Console\Commands\SyncReservationsAfterDateCommand;
use App\Jobs\SyncReservationToPersonio;
use App\Models\Reservation;

it('syncs reservations after date', function () {
    $this->artisan(SyncReservationsAfterDateCommand::class, ['date' => '2023-01-01'])
        ->expectsOutput('Syncing reservations after a certain date...')
        ->assertExitCode(0);
})->group('personio');

it('pushes all reservation jobs to the queue of found reservations', function () {
    Queue::fake();

    Reservation::factory()->count(5)->create([
        'personio_id' => null,
        'created_at' => '2023-01-03',
    ]);

    $this->artisan(SyncReservationsAfterDateCommand::class, ['date' => '2023-01-01'])
        ->expectsOutput('Syncing reservations after a certain date...')
        ->expectsOutput('Found 5 reservations to sync.')
        ->assertExitCode(0);

    Queue::assertPushed(SyncReservationToPersonio::class, 5);
})->group('personio');

<?php

namespace App\Console\Commands;

use App\Jobs\SyncReservationToPersonio;
use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;

class SyncReservationsAfterDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-reservations-after-date-command
                                {date? : The date to sync reservations after. (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tries to sync reservations after a certain date.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $date = $this->argument('date');
        if (! $date) {
            $date = $this->ask('What date should we sync reservations after? (YYYY-MM-DD)');
        }
        try {
            $date = Carbon::parse($date);
        } catch (InvalidFormatException) {
            $this->error('Invalid date format. Please use YYYY-MM-DD.');

            return;
        }

        $this->info('Syncing reservations after a certain date...');

        $reservations = Reservation::query()
            ->whereNull('personio_id')
            ->where('created_at', '>=', $date)
            ->get();

        $this->info('Found '.$reservations->count().' reservations to sync.');

        $reservations->each(function (Reservation $reservation) {
            SyncReservationToPersonio::dispatch($reservation);
        });

        $this->info('Done!');
    }
}

<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Team;
use Http;

class PersonioService
{
    private string $token = '';

    public function __construct(private readonly Team $team)
    {
        if (! $team->personio_client_id || ! $team->personio_client_secret) {
            return;
        }

        $this->login($team->personio_client_id, $team->personio_client_secret);
    }

    private function login(string $clientId, string $clientSecret): void
    {
        $response = Http::personio()
            ->post('auth', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

        if ($response->successful() && $response->json('success')) {
            $this->token = $response->json('data.token');
        }
    }

    public function syncAllEmployees(): void
    {
        $response = Http::personio()
            ->withToken($this->token)
            ->get('company/employees?limit=100');

        if (! $response->successful() || ! $response->json('success')) {
            return;
        }

        $users = $this->team
            ->allUsers()
            ->whereIn('email', collect($response->json('data'))->pluck('attributes.email.value'));

        collect($response->json('data'))->each(function ($personioUser) use ($users) {
            $email = data_get($personioUser, 'attributes.email.value');
            $user = $users->firstWhere('email', $email);

            if (! $user) {
                return;
            }

            $user->personio_id = data_get($personioUser, 'attributes.id.value');
            $user->save();
        });
    }

    public function syncAllTimeOffTypes(): void
    {
        $response = Http::personio()
            ->withToken($this->token)
            ->get('company/time-off-types');

        if (! $response->successful() || ! $response->json('success')) {
            return;
        }

        $timeOffTypes = collect($response->json('data'))
            ->map(fn ($timeOffType) => [
                'team_id' => $this->team->id,
                'personio_id' => data_get($timeOffType, 'attributes.id'),
                'name' => data_get($timeOffType, 'attributes.name'),
            ])->toArray();

        $this->team->timeOffTypes()->upsert($timeOffTypes, ['personio_id'], ['name', 'team_id']);
    }

    public function syncReservation(Reservation $reservation): void
    {
        $response = Http::personio()
            ->withToken($this->token)
            ->post('/company/time-offs', [
                'employee_id' => $reservation->user->personio_id,
                'time_off_type_id' => $reservation->table->timeOffType->personio_id,
                'start_date' => $reservation->date->format('Y-m-d'),
                'end_date' => $reservation->date->format('Y-m-d'),
                'half_day_start' => false,
                'half_day_end' => false,
                'comment' => 'Automatically created by Reservation System',
                'skip_approval' => true,
            ]);

        if (! $response->successful() || ! $response->json('success')) {
            return;
        }

        $reservation->update([
            'personio_id' => $response->json('data.attributes.id'),
        ]);
    }
}

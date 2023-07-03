<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Team;
use Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Log;

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
        if ($this->team->personio_token) {
            $this->token = $this->team->personio_token;

            Log::warning('Personio token already exists', [
                'token' => $this->token,
            ]);

            return;
        }

        $response = Http::personio()
            ->post('auth', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

        if ($response->successful() && $response->json('success')) {
            $this->token = $response->json('data.token');
            $this->team->forceFill([
                'personio_token' => $this->token,
            ]);
            $this->team->save();

            Log::warning('Personio token created', [
                'token' => $this->token,
            ]);
        } else {
            $this->token = '';
            $this->team->forceFill([
                'personio_token' => null,
            ]);
            $this->team->save();

            Log::error('Could not login to Personio', [
                'response' => $response->json(),
            ]);
        }
    }

    /**
     * The token is a single use token, so we need to update it after every request.
     * If the request was not successful, we don't get a new token.
     * If the request was successful, we get a new token. The
     * api is rate limited, so we need to make sure that we
     * save the current token on the team model, so that
     * we don't have to log in again on construct.
     */
    private function updateToken(Response $response): void
    {
        Log::warning('Personio token updated', [
            'token' => $this->token,
            'response' => $response->json(),
        ]);


        if ($response->successful() && $response->json('success')) {
            $this->token = Str::remove('Bearer ', $response->header('authorization'));
            $this->team->forceFill([
                'personio_token' => $this->token,
            ]);
            $this->team->save();
        } elseif ($response->successful() && ! $response->json('success')) {
            $this->token = '';
            $this->team->forceFill([
                'personio_token' => null,
            ]);
            $this->team->save();
        }
    }

    public function syncAllEmployees(): void
    {
        $response = Http::personio()
            ->withToken($this->token)
            ->get('company/employees?limit=100');

        $this->updateToken($response);

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

        $this->updateToken($response);

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
        if ($reservation->table?->timeOffType?->personio_id === null) {
            Log::warning('No time off type for table', [
                'reservation' => $reservation->toArray(),
            ]);

            return;
        }

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

        Log::warning('Reservation in Personio', [
            'reservation' => $reservation->toArray(),
            'response' => $response->json(),
        ]);

        $this->updateToken($response);

        if (! $response->successful() || ! $response->json('success')) {
            Log::error('Could not create reservation in Personio', [
                'reservation' => $reservation->toArray(),
                'response' => $response->json(),
            ]);

            return;
        }

        $reservation->update([
            'personio_id' => $response->json('data.attributes.id'),
        ]);
    }

    public function deleteReservation(Reservation $reservation): void
    {
        if (! $reservation->personio_id) {
            return;
        }

        $response = Http::personio()
            ->withToken($this->token)
            ->delete('/company/time-offs/'.$reservation->personio_id);

        $this->updateToken($response);

        if (! $response->successful() || ! $response->json('success')) {
            Log::error('Could not delete reservation in Personio', [
                'reservation' => $reservation->toArray(),
                'response' => $response->json(),
            ]);

            return;
        }

        $reservation->update([
            'personio_id' => null,
        ]);
    }
}

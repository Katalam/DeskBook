<?php

namespace App\Services;


use App\Models\Team;
use Http;

class PersonioService
{
    private string $token = '';

    public function __construct(private readonly Team $team)
    {
        if (!$team->personio_client_id || !$team->personio_client_secret) {
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

        if (!$response->successful() || !$response->json('success')) {
            return;
        }

        $users = $this->team
            ->allUsers()
            ->whereIn('email', collect($response->json('data'))->pluck('attributes.email.value'));

        collect($response->json('data'))->each(function ($personioUser) use ($users) {
            $email = data_get($personioUser, 'attributes.email.value');
            $user = $users->firstWhere('email', $email);

            if (!$user) {
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

        if (!$response->successful() || !$response->json('success')) {
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
}

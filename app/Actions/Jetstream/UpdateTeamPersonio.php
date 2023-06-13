<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\UpdatesTeamNames;

class UpdateTeamPersonio implements UpdatesTeamNames
{
    /**
     * Validate and update the given team's name.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, Team $team, array $input): void
    {
        Gate::forUser($user)->authorize('update', $team);

        Validator::make($input, [
            'personio_client_id' => ['required', 'string', 'max:255'],
            'personio_client_secret' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateTeamPersonio');

        $team->forceFill([
            'personio_client_id' => $input['personio_client_id'],
            'personio_client_secret' => $input['personio_client_secret'],
        ])->save();
    }
}

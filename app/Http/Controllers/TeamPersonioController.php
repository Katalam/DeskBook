<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TeamPersonioController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Team $team)
    {
        Gate::forUser($request->user())->authorize('update', $team);

        Validator::make($request->all(), [
            'personio_client_id' => ['required', 'string', 'max:255'],
            'personio_client_secret' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateTeamPersonio');

        $team->forceFill([
            'personio_client_id' => $request->input('personio_client_id'),
            'personio_client_secret' => $request->input('personio_client_secret'),
            'personio_token' => null,
        ])->save();

        return back(303);
    }
}

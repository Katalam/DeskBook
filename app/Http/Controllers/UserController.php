<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $queryReservations = static function ($query) {
            return $query
                ->orderBy('date')
                ->where('date', '>', today()->subDay())
                ->with('table');
        };

        // we need to load the reservations for the team users and the owner separately
        $team = $request->user()->currentTeam->load([
            'users.reservations' => $queryReservations,
            'owner.reservations' => $queryReservations,
        ]);

        $users = $team->allUsers();

        return inertia('Users/Index', [
            'users' => UserResource::collection($users),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationResource;
use App\Http\Resources\TableResource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $reservation = auth()->user()
            ?->reservations()
            ->with('table.room')
            ->where('date', today())
            ->first();

        $favorites = $request->user()
            ?->favorites()
            ->with('room')
            ->get();

        return inertia('Dashboard', [
            'reservation' => $reservation ? ReservationResource::make($reservation) : null,
            'favorites' => TableResource::collection($favorites),
            'today' => today()->format('Y-m-d'),
        ]);
    }
}

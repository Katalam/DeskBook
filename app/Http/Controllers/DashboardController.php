<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\TableResource;
use App\Models\Room;
use App\Models\Table;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $reservation = auth()->user()
            ?->reservations()
            ->with('table.room')
            ->where('date', today())
            ->first();

        return inertia('Dashboard', [
            'reservation' => $reservation ? ReservationResource::make($reservation) : null,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Http\Resources\TableResource;
use App\Models\Room;
use App\Models\Table;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $rooms = Room::query()
            ->get();

        $tables = Table::query()
            ->get();

        return inertia('Dashboard', [
            'rooms' => RoomResource::collection($rooms),
            'tables' => TableResource::collection($tables),
        ]);
    }
}

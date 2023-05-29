<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Http\Resources\TableResource;
use App\Models\Room;
use App\Models\Table;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $rooms = Room::query()
            ->get();

        $tables = Table::query()
            ->get();

        return inertia('Setting', [
            'rooms' => RoomResource::collection($rooms),
            'tables' => TableResource::collection($tables),
        ]);
    }
}

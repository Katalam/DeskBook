<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeatureResource;
use App\Http\Resources\NotificationResource;
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
            ->where('team_id', $request->user()->currentTeam->id)
            ->get();

        $tables = Table::query()
            ->whereIn('room_id', $rooms->pluck('id')->toArray())
            ->get();

        $features = $request->user()->currentTeam->features;

        $notifications = $request->user()->currentTeam->notifications;

        return inertia('Settings/Index', [
            'rooms' => RoomResource::collection($rooms),
            'tables' => TableResource::collection($tables),
            'features' => FeatureResource::collection($features),
            'notifications' => NotificationResource::collection($notifications),
        ]);
    }
}

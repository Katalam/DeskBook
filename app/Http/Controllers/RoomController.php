<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\RoomUpdateRequest;
use App\Http\Resources\RoomResource;
use App\Http\Resources\TableResource;
use App\Models\Room;
use App\Models\Table;
use App\Models\TimeOffType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(RoomStoreRequest $request): RedirectResponse
    {
        // merge the team id into the request data and create the room
        // this will automatically associate the room with the current team
        $room = Room::create($request->safe()->merge([
            'team_id' => $request->user()->currentTeam->id,
        ])->all());

        return redirect()->route('rooms.edit', $room->id);
    }

    public function create()
    {
        return inertia('Settings/Rooms/Create');
    }

    public function edit(Room $room)
    {
        $this->authorize('update', $room);

        $tables = Table::query()
            ->where('room_id', $room->id)
            ->get();

        return inertia('Settings/Rooms/Edit', [
            'room' => RoomResource::make($room),
            'tables' => TableResource::collection($tables),
        ]);
    }

    public function update(RoomUpdateRequest $request, int $room): RedirectResponse
    {
        Room::query()
            ->where('id', $room)
            ->update($request->safe()->all());

        return redirect()->back();
    }

    public function destroy(int $room): RedirectResponse
    {
        Room::query()
            ->where('id', $room)
            ->delete();

        return redirect()->route('settings');
    }
}

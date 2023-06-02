<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\RoomUpdateRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;

class RoomController extends Controller
{
    public function store(RoomStoreRequest $request): RedirectResponse
    {
        // merge the team id into the request data and create the room
        // this will automatically associate the room with the current team
        Room::create($request->safe()->merge([
            'team_id' => $request->user()->currentTeam->id,
        ])->all());

        return redirect()->back();
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

        return redirect()->back();
    }
}

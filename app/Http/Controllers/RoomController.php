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
        Room::create($request->safe()->all());

        return redirect()->back();
    }

    public function update(RoomUpdateRequest $request, int $room): RedirectResponse
    {
        Room::query()
            ->where('id', $room)
            ->update($request->safe()->all());

        return redirect()->back();
    }
}

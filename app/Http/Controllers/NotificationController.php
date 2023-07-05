<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\RoomResource;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function create()
    {
        return inertia('Settings/Notifications/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationRequest $request): RedirectResponse
    {
        $notification = $request->user()->currentTeam->notifications()->create($request->safe()->except('rooms'));

        if ($request->has('rooms')) {
            $notification->rooms()->sync($request->input('rooms', []));
        }

        return redirect()->route('notifications.edit', $notification->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->load('team.rooms', 'rooms');

        $rooms = $notification->team->rooms;

        return inertia('Settings/Notifications/Edit', [
            'notification' => NotificationResource::make($notification),
            'rooms' => RoomResource::collection($rooms),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification): RedirectResponse
    {
        $notification->update($request->safe()->except('rooms'));

        if ($request->has('rooms')) {
            $notification->rooms()->sync($request->input('rooms', []));
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('settings');
    }
}

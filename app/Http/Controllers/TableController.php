<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableStoreRequest;
use App\Http\Requests\TableUpdateRequest;
use App\Http\Resources\RoomResource;
use App\Http\Resources\TableResource;
use App\Models\Room;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = today();

        if ($request->has('date')) {
            $selectedDate = Carbon::parse($request->input('date'));
        }

        $rooms = Room::query()
            ->where('team_id', $request->user()->currentTeam->id)
            ->when($request->has('room'), function ($query) use ($request) {
                return $query->where('id', $request->input('room'));
            })
            ->with([
                'tables' => function ($query) use ($selectedDate) {
                    return $query
                        ->with([
                            'reservations' => function ($query) use ($selectedDate) {
                                return $query
                                    ->where('date', '=', $selectedDate)
                                    ->with('user');
                            },
                            'favorites' => function ($query) {
                                return $query->where('id', auth()->id());
                            },
                        ]);
                },
            ])
            ->has('tables')
            ->get();

        return inertia('Tables/Index', [
            'rooms' => RoomResource::collection($rooms),
            'dates' => [
                // the subDay and addDay function are changing the selectedDate variable,
                // so we need to add a day for selected and one more to after to get the correct dates
                'before' => $selectedDate->subDay()->format('Y-m-d'),
                'selectedWeekday' => $selectedDate->addDay()->dayName,
                'selectedDate' => $selectedDate->format('Y-m-d'),
                'isToday' => $selectedDate->isToday(),
                'today' => today()->format('Y-m-d'),
                'after' => $selectedDate->addDay()->format('Y-m-d'),
            ],
            'hasBookedSelectedDate' => $request->user()->reservations()->where('date', $selectedDate->subDay())->exists(),
        ]);
    }

    public function store(TableStoreRequest $request): RedirectResponse
    {
        Table::create($request->safe()->all());

        return redirect()->back();
    }

    public function update(TableUpdateRequest $request, int $table): RedirectResponse
    {
        Table::query()
            ->where('id', $table)
            ->update($request->safe()->all());

        return redirect()->back();
    }

    public function destroy(int $table): RedirectResponse
    {
        Table::query()
            ->where('id', $table)
            ->delete();

        return redirect()->back();
    }
}

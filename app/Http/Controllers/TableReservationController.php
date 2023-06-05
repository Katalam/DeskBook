<?php

namespace App\Http\Controllers;

use App\Events\TableReserved;
use App\Http\Requests\TableReservationStoreRequest;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TableReservationController extends Controller
{
    public function store(TableReservationStoreRequest $request, Table $table): RedirectResponse
    {
        $alreadyReserved = $table
            ->reservations()
            ->where('date', $request->date)
            ->exists();

        if ($alreadyReserved && !$table->multiple_bookings) {
            return back()->withErrors([
                'date' => 'Dieser Tisch ist an diesem Tag bereits reserviert.',
            ]);
        }

        $alreadySomethingReserved = $request->user()
            ->reservations()
            ->where('date', $request->date)
            ->exists();

        if ($alreadySomethingReserved) {
            return back()->withErrors([
                'date' => 'Du hast bereits einen Tisch an diesem Tag reserviert.',
            ]);
        }

        $table->reservations()->create([
            'date' => $request->date,
            'user_id' => auth()->id(),
        ]);

        broadcast(new TableReserved($request->user()->currentTeam))->toOthers();

        return redirect()->route('tables.index');
    }

    public function destroy(Request $request, Table $table, int $reservation): RedirectResponse
    {
        $table->reservations()
            ->where('id', $reservation)
            ->where('user_id', auth()->id())
            ->delete();

        broadcast(new TableReserved($request->user()->currentTeam))->toOthers();

        return redirect()->back();
    }
}

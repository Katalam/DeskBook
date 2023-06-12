<?php

namespace App\Http\Controllers;

use App\Events\TableReserved;
use App\Http\Requests\TableReservationStoreRequest;
use App\Jobs\SyncReservationDeleteToPersonio;
use App\Jobs\SyncReservationToPersonio;
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

        if ($alreadyReserved && ! $table->multiple_bookings) {
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

        $reservation = $table->reservations()->create([
            'date' => $request->date,
            'user_id' => auth()->id(),
        ]);

        broadcast(new TableReserved($request->user()->currentTeam))->toOthers();
        SyncReservationToPersonio::dispatch($reservation);

        return redirect()->route('tables.index');
    }

    public function destroy(Request $request, Table $table, int $reservation): RedirectResponse
    {
        $reservationModel = $table->reservations()->find($reservation);

        if (! $reservationModel) {
            return redirect()->back();
        }

        $reservationModel->delete();

        broadcast(new TableReserved($request->user()->currentTeam))->toOthers();

        if ($table->time_off_type_id) {
            SyncReservationDeleteToPersonio::dispatch($reservationModel->personio_id);
        }

        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableReservationStoreRequest;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;

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

        $table->reservations()->create([
            'date' => $request->date,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('tables.index');
    }

    public function destroy(Table $table, int $reservation): RedirectResponse
    {
        $table->reservations()
            ->where('id', $reservation)
            ->where('user_id', auth()->id())
            ->delete();

        return redirect()->back();
    }
}

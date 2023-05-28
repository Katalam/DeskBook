<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableReservationStoreRequest;
use App\Models\Table;

class TableReservationController extends Controller
{
    public function create(int $table)
    {
        return inertia('Tables/Create', [
            'table' => $table,
        ]);
    }

    public function store(TableReservationStoreRequest $request, Table $table)
    {
        $alreadyReserved = $table
            ->reservations()
            ->where('date', $request->date)
            ->exists();

        if ($alreadyReserved) {
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
}

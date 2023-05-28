<?php

namespace App\Http\Controllers;

use App\Http\Resources\TableResource;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = today();

        if ($request->has('date')) {
            $selectedDate = Carbon::parse($request->input('date'));
        }

        $tables = Table::query()
            ->with([
                'reservations' => function ($query) use ($selectedDate) {
                    return $query
                        ->where('date', '=', $selectedDate)
                        ->with('user');
                }])
            ->get();

        return inertia('Tables/Index', [
            'tables' => TableResource::collection($tables),
            'dates' => [
                // the subDay and addDay function are changing the selectedDate variable,
                // so we need to add a day for selected and one more to after to get the correct dates
                'before' => $selectedDate->subDay()->format('Y-m-d'),
                'selectedDate' => $selectedDate->addDay()->format('Y-m-d'),
                'after' => $selectedDate->addDay()->format('Y-m-d'),
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\TableResource;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::query()
            ->with(['reservations' => fn($query) => $query->with('user')->orderBy('date')])
            ->get();

        return inertia('Tables/Index', [
            'tables' => TableResource::collection($tables),
        ]);
    }
}

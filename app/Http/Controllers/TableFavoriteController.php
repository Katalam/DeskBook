<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableFavoriteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Table $table)
    {
        $request->user()->favorites()->toggle([
            $table->id,
        ]);

        return response()->noContent();
    }
}

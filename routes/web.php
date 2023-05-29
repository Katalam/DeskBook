<?php

use App\Http\Controllers\TableController;
use App\Http\Controllers\TableReservationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::controller(TableController::class)->group(function () {
        Route::get('/tables', 'index')->name('tables.index');
        Route::post('/tables', 'store')->name('tables.store');
    });

    Route::controller(TableReservationController::class)
        ->prefix('/tables/{table}/reservations')
        ->as('tables.reservations')
        ->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
        });
});

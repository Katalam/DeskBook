<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
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
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::controller(RoomController::class)
        ->prefix('/rooms')
        ->as('rooms.')
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::patch('{room}', 'update')->name('update');
        });

    Route::controller(TableController::class)
        ->prefix('/tables')
        ->as('tables.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::patch('{table}', 'update')->name('update');
        });

    Route::controller(TableReservationController::class)
        ->prefix('/tables/{table}/reservations')
        ->as('tables.reservations.')
        ->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
        });
});

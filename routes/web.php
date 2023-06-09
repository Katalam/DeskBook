<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TableFavoriteController;
use App\Http\Controllers\TableReservationController;
use App\Http\Controllers\TeamPersonioController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckFor2FaAuthentication;
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
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

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
    CheckFor2FaAuthentication::class,
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/settings', SettingController::class)->name('settings');

    Route::controller(RoomController::class)
        ->prefix('/rooms')
        ->as('rooms.')
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('create', 'create')->name('create');
            Route::get('{room}', 'edit')->name('edit');
            Route::patch('{room}', 'update')->name('update');
            Route::delete('{room}', 'destroy')->name('destroy');
        });

    Route::controller(TableController::class)
        ->prefix('/tables')
        ->as('tables.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('create', 'create')->name('create');
            Route::get('{table}', 'edit')->name('edit');
            Route::patch('{table}', 'update')->name('update');
            Route::delete('{table}', 'destroy')->name('destroy');
        });

    Route::controller(TableReservationController::class)
        ->prefix('/tables/{table}/reservations')
        ->as('tables.reservations.')
        ->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::delete('{reservation}', 'destroy')->name('destroy');
        });

    Route::post('/tables/{table}/favorite', TableFavoriteController::class)->name('tables.favorite.toggle');

    Route::controller(UserController::class)
        ->prefix('/users')
        ->as('users.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::patch('{user}', 'update')->name('update');
            Route::delete('{user}', 'destroy')->name('destroy');
        });

    Route::controller(FeatureController::class)
        ->prefix('features')
        ->as('features.')
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('create', 'create')->name('create');
            Route::get('{feature}', 'edit')->name('edit');
            Route::patch('{feature}', 'update')->name('update');
            Route::delete('{feature}', 'destroy')->name('destroy');
        });

    Route::controller(NotificationController::class)
        ->prefix('notifications')
        ->as('notifications.')
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('create', 'create')->name('create');
            Route::get('{notification}', 'edit')->name('edit');
            Route::patch('{notification}', 'update')->name('update');
            Route::delete('{notification}', 'destroy')->name('destroy');
        });

    Route::put('/teams/{team}/personio', TeamPersonioController::class)->name('teams.personio.update');
});

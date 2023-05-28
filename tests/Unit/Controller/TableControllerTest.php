<?php

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('will return a 200 response on table index', function () {
    $user = User::factory()->create();
    Table::factory()
        ->has(
            Reservation::factory()
                ->for($user)
                ->count(3))
        ->count(15)
        ->create();

    $response = $this->actingAs($user)->get(route('tables.index'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tables/Index')
        ->has('tables')
        ->has('tables.data', 15, fn (Assert $page) => $page
            ->has('id')
            ->has('name')
            ->has('location')
            ->has('reserved')
            ->has('reservations')
        )
        ->has('dates')
    );
})->group('unit', 'controller', 'table');

it('will return a 200 response on table index with get parameter date', function () {
    $user = User::factory()->create();
    Table::factory()
        ->has(
            Reservation::factory()
                ->for($user)
                ->count(3))
        ->count(15)
        ->create();

    $response = $this->actingAs($user)->get(route('tables.index', ['date' => now()->addDay()->format('Y-m-d')]));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tables/Index')
        ->has('tables')
        ->where('dates.selectedDate', now()->addDay()->format('Y-m-d'))
    );
})->group('unit', 'controller', 'table');


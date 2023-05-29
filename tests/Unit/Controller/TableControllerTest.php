<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Table;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('will return a 200 response on table index', function () {
    $user = User::factory()->create();
    Room::factory()
        ->has(
            Table::factory()
                ->has(
                    Reservation::factory()
                        ->for($user)
                        ->count(3),
                    'reservations'
                )
                ->count(5),
            'tables'
        )
        ->count(3)
        ->create();

    $response = $this->actingAs($user)->get(route('tables.index'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tables/Index')
        ->has('rooms')
        ->has('rooms.data', 3, fn (Assert $page) => $page
            ->has('id')
            ->has('name')
            ->has('tables', 5, fn (Assert $page) => $page
                ->has('id')
                ->has('name')
                ->has('location')
                ->has('reserved')
                ->has('reservation')
            )
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
        ->has('rooms')
        ->where('dates.selectedDate', now()->addDay()->format('Y-m-d'))
    );
})->group('unit', 'controller', 'table');

it('will create a new table on store method', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    $response = $this->actingAs($user)->post(route('tables.store'), [
        'name' => 'Test Table',
        'location' => 'Test Location',
        'room_id' => $room->id,
    ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('tables', [
        'name' => 'Test Table',
        'location' => 'Test Location',
        'room_id' => $room->id,
    ]);
})->group('unit', 'controller', 'table');

it('will update a table on update method', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    $table = Table::factory()
        ->state([
            'name' => 'Old Name',
            'location' => 'Old Location',
            'room_id' => $room->id,
        ])
        ->create();

    $response = $this->actingAs($user)->patch(route('tables.update', $table->id), [
        'name' => 'Test Table',
        'location' => 'Test Location',
    ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('tables', [
        'name' => 'Test Table',
        'location' => 'Test Location',
    ]);
    $this->assertDatabaseMissing('tables', [
        'name' => 'Old Name',
        'location' => 'Old Location',
    ]);
})->group('unit', 'controller', 'table');

it('can destroy a table', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();

    $response = $this->actingAs($user)->delete(route('tables.destroy', $table->id));

    $response->assertStatus(302);

    $this->assertDatabaseMissing('tables', [
        'id' => $table->id,
    ]);
})->group('unit', 'controller', 'table');

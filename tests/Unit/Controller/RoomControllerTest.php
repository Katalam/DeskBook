<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Table;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('will create a new room on store method', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('rooms.store'), [
        'name' => 'Test Room',
    ]);

    $response->assertRedirect(route('tables.index'));

    $this->assertDatabaseHas('rooms', [
        'name' => 'Test Room',
    ]);
})->group('unit', 'controller', 'room');

it('will update a room on update method', function () {
    $user = User::factory()->create();
    $room = Room::factory()
        ->state([
            'name' => 'Old Room',
        ])
        ->create();

    $response = $this->actingAs($user)->patch(route('rooms.update', $room->id), [
        'name' => 'Test Room',
    ]);

    $response->assertRedirect(route('tables.index'));

    $this->assertDatabaseHas('rooms', [
        'name' => 'Test Room',
    ]);
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Old Room',
    ]);
})->group('unit', 'controller', 'room');

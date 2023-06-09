<?php

use App\Models\Room;
use App\Models\User;

it('will create a new room on store method', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $room = Room::factory()->make()->toArray();

    $response = $this->actingAs($user)->post(route('rooms.store'), $room);

    $response->assertStatus(302);

    $this->assertDatabaseHas('rooms', [
        'name' => $room['name'],
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

    $response->assertStatus(302);

    $this->assertDatabaseHas('rooms', [
        'name' => 'Test Room',
    ]);
    $this->assertDatabaseMissing('rooms', [
        'name' => 'Old Room',
    ]);
})->group('unit', 'controller', 'room');

it('can destroy a room', function () {
    $user = User::factory()->create();
    $room = Room::factory()->create();

    $response = $this->actingAs($user)->delete(route('rooms.destroy', $room->id));

    $response->assertStatus(302);

    $this->assertDatabaseMissing('rooms', [
        'id' => $room->id,
    ]);
})->group('unit', 'controller', 'room');

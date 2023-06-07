<?php

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('will return a 302 response on table favorite toggle', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();
    $table = Table::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('tables.favorite.toggle', $table));

    $response->assertStatus(204);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('user_table_favorites', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
})->group('unit', 'controller', 'table-favorite');

it('will return a 302 response on table favorite toggle with toggled on', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();
    $table = Table::factory()->create();

    $user->favorites()->attach($table->id);

    $response = $this->actingAs($user)
        ->post(route('tables.favorite.toggle', $table));

    $response->assertStatus(204);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('user_table_favorites', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
})->group('unit', 'controller', 'table-favorite');

it('will return a 404 response when the table is not found', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $response = $this->actingAs($user)
        ->post(route('tables.favorite.toggle', 9999));

    $response->assertStatus(404);
})->group('unit', 'controller', 'table-favorite');

<?php

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('will return a 200 response on table reservation create', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('tables.reservations.create', $table));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tables/Reservations/Create')
        ->where('table', $table->id)
    );
})->group('unit', 'controller', 'table-reservation');

it('will return a 302 response on table reservation store', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('tables.reservations.store', $table), [
            'date' => now()->format('Y-m-d'),
        ]);

    $response->assertStatus(302);

    $response->assertRedirectToRoute('tables.index');

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
})->group('unit', 'controller', 'table-reservation');

it('will return an error response on table reservation store if date is blocked', function () {
    $user = User::factory()->create();
    $userForReservation = User::factory()->create();
    $table = Table::factory()->create();

    $table->reservations()->create([
        'date' => now()->format('Y-m-d'),
        'user_id' => $userForReservation->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('tables.reservations.store', $table), [
            'date' => now()->format('Y-m-d'),
        ]);

    $response->assertStatus(302);

    $response->assertSessionHasErrors();

    $this->assertDatabaseMissing('reservations', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'user_id' => $userForReservation->id,
    ]);
})->group('unit', 'controller', 'table-reservation');


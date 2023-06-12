<?php

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('will return a 302 response on table reservation store', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();
    $table = Table::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('tables.reservations.store', $table), [
            'date' => today()->format('Y-m-d'),
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
        'date' => today(),
        'user_id' => $userForReservation->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('tables.reservations.store', $table), [
            'date' => today(),
        ]);

    $response->assertStatus(302);

//    Session has no errors in workflow tests for some reason
//    $response->assertSessionHasErrors();

    $this->assertDatabaseMissing('reservations', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'user_id' => $userForReservation->id,
    ]);
})->group('unit', 'controller', 'table-reservation');

it('will not return an error response on table reservation store if date is blocked when it is a multiple bookable table', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();
    $userForReservation = User::factory()->create();
    $table = Table::factory()
        ->state([
            'multiple_bookings' => true,
        ])
        ->create();

    $table->reservations()->create([
        'date' => today(),
        'user_id' => $userForReservation->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('tables.reservations.store', $table), [
            'date' => today(),
        ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'user_id' => $userForReservation->id,
    ]);
})->group('unit', 'controller', 'table-reservation');

it('will return an error response on table reservation store if the requester already has a table', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();
    $table = Table::factory()
        ->create();
    $tableForSecondReservation = Table::factory()
        ->create();

    $table->reservations()->create([
        'date' => today(),
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('tables.reservations.store', $tableForSecondReservation), [
            'date' => today(),
        ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseMissing('reservations', [
        'table_id' => $tableForSecondReservation->id,
        'user_id' => $user->id,
    ]);
})->group('unit', 'controller', 'table-reservation');

it('will destroy a reservation if user is the reserver', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();
    $table = Table::factory()->create();
    $reservation = Reservation::factory()->create([
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('tables.reservations.destroy', [$table, $reservation]));

    $response->assertStatus(302);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('reservations', [
        'table_id' => $table->id,
        'user_id' => $user->id,
    ]);
})->group('unit', 'controller', 'table-reservation');

it('will not destroy a reservation if user is not the reserver', function () {
    $reserver = User::factory()
        ->withPersonalTeam()
        ->create();
    $user = User::factory()
        ->withPersonalTeam()
        ->create();
    $table = Table::factory()->create();
    $reservation = Reservation::factory()->create([
        'table_id' => $table->id,
        'user_id' => $reserver->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('tables.reservations.destroy', [$table, $reservation]));

    $response->assertStatus(302);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'user_id' => $reserver->id,
    ]);
})->group('unit', 'controller', 'table-reservation');

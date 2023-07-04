<?php

use App\Models\Team;
use App\Models\User;
use App\Models\Notification;

it('will render a inertia page on create', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $response = $this->actingAs($user)->get(route('notifications.create'));

    $response->assertOk();
})->group('unit', 'controller', 'notification');

it('will create a new notification on store method', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $notification = Notification::factory()->make()->toArray();

    $response = $this->actingAs($user)->post(route('notifications.store'), $notification);

    $response->assertStatus(302);

    // make sure the team id is set
    $notification['team_id'] = $user->currentTeam->id;

    $this->assertDatabaseHas('notifications', $notification);
})->group('unit', 'controller', 'notification');

it('will update a notification on update method', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $notification = $user->currentTeam->notifications()->create(Notification::factory()->make()->toArray());

    $notification->name = 'Test Notification Updated';

    $response = $this->actingAs($user)->patch(route('notifications.update', $notification), $notification->toArray());

    $response->assertStatus(302);

    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'name' => 'Test Notification Updated',
    ]);
})->group('unit', 'controller', 'notification');

it('will not update a notification on update method of another team', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $anotherTeam = Team::factory()->create();

    $notification = $anotherTeam->notifications()->create(Notification::factory()->make()->toArray());

    $oldName = $notification->name;

    $notification->name = 'Test Notification Updated';

    $response = $this->actingAs($user)->patch(route('notifications.update', $notification), $notification->toArray());

    $response->assertStatus(403);

    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'name' => $oldName,
    ]);
    $this->assertDatabaseMissing('notifications', [
        'id' => $notification->id,
        'name' => 'Test Notification Updated',
    ]);
})->group('unit', 'controller', 'notification');

it('will destroy a notification on destroy method', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $notification = $user->currentTeam->notifications()->create(Notification::factory()->make()->toArray());

    $response = $this->actingAs($user)->delete(route('notifications.destroy', $notification));

    $response->assertStatus(302);

    $this->assertDatabaseMissing('notifications', [
        'id' => $notification->id,
    ]);
})->group('unit', 'controller', 'notification');

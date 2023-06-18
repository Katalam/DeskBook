<?php

use App\Models\Team;
use App\Models\User;

it('will render a inertia page on create', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $response = $this->actingAs($user)->get(route('features.create'));

    $response->assertOk();
})->group('unit', 'controller', 'feature');

it('will create a new feature on store method', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $response = $this->actingAs($user)->post(route('features.store'), [
        'name' => 'Test Feature',
    ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('features', [
        'team_id' => $user->currentTeam->id,
        'name' => 'Test Feature',
    ]);
})->group('unit', 'controller', 'feature');

it('will update a feature on update method', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $feature = $user->currentTeam->features()->create([
        'name' => 'Test Feature',
    ]);

    $response = $this->actingAs($user)->patch(route('features.update', $feature), [
        'name' => 'Test Feature Updated',
    ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('features', [
        'id' => $feature->id,
        'name' => 'Test Feature Updated',
    ]);
})->group('unit', 'controller', 'feature');

it('will not update a feature on update method of another team', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $anotherTeam = Team::factory()->create();

    $feature = $anotherTeam->features()->create([
        'name' => 'Test Feature',
    ]);

    $response = $this->actingAs($user)->patch(route('features.update', $feature), [
        'name' => 'Test Feature Updated',
    ]);

    $response->assertStatus(403);

    $this->assertDatabaseHas('features', [
        'id' => $feature->id,
        'name' => 'Test Feature',
    ]);
    $this->assertDatabaseMissing('features', [
        'id' => $feature->id,
        'name' => 'Test Feature Updated',
    ]);
})->group('unit', 'controller', 'feature');

it('will destroy a feature on destroy method', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $feature = $user->currentTeam->features()->create([
        'name' => 'Test Feature',
    ]);

    $response = $this->actingAs($user)->delete(route('features.destroy', $feature));

    $response->assertStatus(302);

    $this->assertDatabaseMissing('features', [
        'id' => $feature->id,
    ]);
})->group('unit', 'controller', 'feature');

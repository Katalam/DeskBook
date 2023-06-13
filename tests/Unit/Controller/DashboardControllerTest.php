<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('will return a 200 response on table index', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard', fn (Assert $page) => $page
            ->has('reservation')
            ->has('favorites')
            ->has('today')
        )
    );
})->group('unit', 'controller', 'dashboard');

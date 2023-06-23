<?php

namespace Tests\Unit\Controller;

use App\Models\User;
use Str;

it('will update the team personio ids', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $user->currentTeam->update([
        'personio_client_id' => 'MyOldPersonioClientId',
        'personio_client_secret' => 'MyOldPersonioClientSecret',
        'personio_token' => Str::random(20),
    ]);


    $response = $this->actingAs($user)->put(route('teams.personio.update', $user->currentTeam), [
        'personio_client_id' => 'MyPersonioClientId',
        'personio_client_secret' => 'MyPersonioClientSecret',
    ]);

    $response->assertStatus(303);

    $this->assertSame('MyPersonioClientId', $user->currentTeam->fresh()->personio_client_id);
    $this->assertSame('MyPersonioClientSecret', $user->currentTeam->fresh()->personio_client_secret);
    $this->assertNull($user->currentTeam->fresh()->personio_token);

})->group('unit', 'controller', 'team', 'personio');

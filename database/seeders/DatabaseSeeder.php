<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Feature;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Table;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()
            ->withPersonalTeam()
            ->create([
                'name' => 'test@test.com',
                'email' => 'test@test.com',
                'password' => Hash::make('password'),
            ]);

        $invitedUser = User::factory()
            ->create([
                'name' => 'test2@test.com',
                'email' => 'test2@test.com',
                'password' => Hash::make('password'),
            ]);
        $user->currentTeam->users()->attach($invitedUser, ['role' => 'admin']);
        $invitedUser->switchTeam($user->currentTeam);

        Room::factory()
            ->state([
                'team_id' => $user->currentTeam->id,
            ])
            ->has(Table::factory()
                ->has(Reservation::factory()->count(5), 'reservations')
                ->count(5), 'tables')
            ->count(2)->create();

        $user->favorites()->sync(Table::all()->random(5));

        Feature::factory()
            ->state([
                'team_id' => $user->currentTeam->id,
            ])
            ->count(5)
            ->create();
    }
}

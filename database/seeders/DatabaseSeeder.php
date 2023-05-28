<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Reservation;
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
         User::factory()->create([
             'name' => 'test@test.com',
             'email' => 'test@test.com',
             'password' => Hash::make('password'),
         ]);

         Table::factory()
             ->has(Reservation::factory()->count(5), 'reservations')
             ->count(5)->create();
    }
}

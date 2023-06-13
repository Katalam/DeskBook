<?php

use App\Console\Commands\SyncPersonioTimeOffTypesCommand;
use App\Console\Commands\SyncPersonioUsersCommand;
use App\Jobs\SyncPersonioTimeOffTypesJob;
use App\Jobs\SyncPersonioUsersJob;
use App\Jobs\SyncReservationDeleteToPersonio;
use App\Jobs\SyncReservationToPersonio;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Table;
use App\Models\Team;
use App\Models\User;
use App\Services\PersonioService;

it('can make a request to the personio api', function () {
    $user = User::factory()->withPersonalTeam()->create();

    Http::preventStrayRequests();

    $token = Str::random(20);

    Http::fake([
        config('personio.base_url').'/*' => Http::response([
            'success' => true,
            'data' => [
                'token' => $token,
            ],
        ]),
    ]);

    // Create a new instance of the PersonioService
    $personio = new PersonioService($user->currentTeam);

    // Get the reflection object of the PersonioService
    $personioReflectionObject = new ReflectionObject($personio);

    // Assert that the token property is set to the token we got from the fake response
    expect($personioReflectionObject->getProperty('token')->getValue($personio))->toBe($token);

    Http::assertSent(function ($request) {
        return $request->url() === config('personio.base_url').'/auth';
    });
})->group('personio');

it('will not break if the personio api is unavailable', function () {
    $user = User::factory()->withPersonalTeam()->create();

    Http::preventStrayRequests();

    Http::fake([
        config('personio.base_url').'/*' => Http::response([
            'success' => false,
            'error' => [
                'code' => 0,
                'message' => 'Wrong credentials',
            ],
        ]),
    ]);

    // Create a new instance of the PersonioService
    $personio = new PersonioService($user->currentTeam);

    // Get the reflection object of the PersonioService
    $personioReflectionObject = new ReflectionObject($personio);

    // Assert that the token property is set to the token we got from the fake response
    expect($personioReflectionObject->getProperty('token')->getValue($personio))->toBe('');

    Http::assertSent(function ($request) {
        return $request->url() === config('personio.base_url').'/auth';
    });
})->group('personio');

it('can sync all employees with their personio id', function () {
    Http::preventStrayRequests();

    $user = User::factory()->withPersonalTeam()->create();
    $personioId = 1;

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),

        config('personio.base_url').'/*' => Http::response([
            'success' => true,
            'data' => [
                [
                    'type' => 'employee',
                    'attributes' => [
                        'id' => [
                            'label' => 'ID',
                            'value' => $personioId,
                            'type' => 'integer',
                            'universal_id' => true,
                        ],
                        'first_name' => [
                            'label' => 'First name',
                            'value' => 'John',
                            'type' => 'string',
                            'universal_id' => false,
                        ],
                        'last_name' => [
                            'label' => 'Last name',
                            'value' => 'Doe',
                            'type' => 'string',
                            'universal_id' => false,
                        ],
                        'email' => [
                            'label' => 'Email',
                            'value' => $user->email,
                            'type' => 'string',
                            'universal_id' => false,
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    // Create a new instance of the PersonioService
    $personio = new PersonioService($user->currentTeam);

    // Sync all employees
    $personio->syncAllEmployees();

    expect($user->fresh()->personio_id)->toBe($personioId);

    Http::assertSent(function ($request) {
        return $request->url() === config('personio.base_url').'/company/employees?limit=100';
    });
})->group('personio');

it('will create a job for each team to sync the users on sync users command', function () {
    Http::preventStrayRequests();

    User::factory()->withPersonalTeam()->create();

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),
    ]);

    Queue::fake();

    Artisan::call(SyncPersonioUsersCommand::class);

    Queue::assertPushed(SyncPersonioUsersJob::class, Team::count());
})->group('personio');

it('will sync the personio id for users of the team', function () {
    Http::preventStrayRequests();

    $user = User::factory()->withPersonalTeam()->create();
    $personioId = 1;

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),

        config('personio.base_url').'/*' => Http::response([
            'success' => true,
            'data' => [
                [
                    'type' => 'employee',
                    'attributes' => [
                        'id' => [
                            'label' => 'ID',
                            'value' => $personioId,
                            'type' => 'integer',
                            'universal_id' => true,
                        ],
                        'first_name' => [
                            'label' => 'First name',
                            'value' => 'John',
                            'type' => 'string',
                            'universal_id' => false,
                        ],
                        'last_name' => [
                            'label' => 'Last name',
                            'value' => 'Doe',
                            'type' => 'string',
                            'universal_id' => false,
                        ],
                        'email' => [
                            'label' => 'Email',
                            'value' => $user->email,
                            'type' => 'string',
                            'universal_id' => false,
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    Artisan::call(SyncPersonioUsersCommand::class);

    expect($user->fresh()->personio_id)->toBe($personioId);
})->group('personio');

it('will sync the time off types for each team', function () {
    Http::preventStrayRequests();

    $personioId = 1;

    $user = User::factory()
        ->state([
            'personio_id' => $personioId,
        ])
        ->withPersonalTeam()
        ->create();

    $personioTimeOffId = 1;

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),

        config('personio.base_url').'/*' => Http::response([
            'success' => true,
            'data' => [
                [
                    'type' => 'TimeOffType',
                    'attributes' => [
                        'id' => $personioTimeOffId,
                        'name' => 'Home Office',
                        'unit' => 'day',
                        'category' => 'offsite_work',
                        'half_day_requests_enabled' => true,
                        'certification_required' => false,
                        'certification_submission_timeframe' => 0,
                        'substitute_option' => 'disabled',
                        'approval_required' => false,
                        'legacy_category' => 'offsite_work',
                    ],
                ],
                [
                    'type' => 'TimeOffType',
                    'attributes' => [
                        'id' => $personioTimeOffId + 1,
                        'name' => 'Office',
                        'unit' => 'day',
                        'category' => 'offsite_work',
                        'half_day_requests_enabled' => true,
                        'certification_required' => false,
                        'certification_submission_timeframe' => 0,
                        'substitute_option' => 'disabled',
                        'approval_required' => false,
                        'legacy_category' => 'offsite_work',
                    ],
                ],
            ],
        ]),
    ]);

    // Create a new instance of the PersonioService
    $personio = new PersonioService($user->currentTeam);

    $personio->syncAllTimeOffTypes();

    expect($user->currentTeam->timeOffTypes->count())->toBe(2);
})->group('personio');

it('will create a job for each team to sync the time off types on sync time off types command', function () {
    Http::preventStrayRequests();

    User::factory()->withPersonalTeam()->create();

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),
    ]);

    Queue::fake();

    Artisan::call(SyncPersonioTimeOffTypesCommand::class);

    Queue::assertPushed(SyncPersonioTimeOffTypesJob::class, Team::count());
})->group('personio');

it('will sync the time off types for a team', function () {
    Http::preventStrayRequests();

    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $personioTimeOffId = 1;

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),

        config('personio.base_url').'/*' => Http::response([
            'success' => true,
            'data' => [
                [
                    'type' => 'TimeOffType',
                    'attributes' => [
                        'id' => $personioTimeOffId,
                        'name' => 'Home Office',
                        'unit' => 'day',
                        'category' => 'offsite_work',
                        'half_day_requests_enabled' => true,
                        'certification_required' => false,
                        'certification_submission_timeframe' => 0,
                        'substitute_option' => 'disabled',
                        'approval_required' => false,
                        'legacy_category' => 'offsite_work',
                    ],
                ],
                [
                    'type' => 'TimeOffType',
                    'attributes' => [
                        'id' => $personioTimeOffId + 1,
                        'name' => 'Office',
                        'unit' => 'day',
                        'category' => 'offsite_work',
                        'half_day_requests_enabled' => true,
                        'certification_required' => false,
                        'certification_submission_timeframe' => 0,
                        'substitute_option' => 'disabled',
                        'approval_required' => false,
                        'legacy_category' => 'offsite_work',
                    ],
                ],
            ],
        ]),
    ]);

    Artisan::call(SyncPersonioTimeOffTypesCommand::class);

    expect($user->currentTeam->fresh()->timeOffTypes->count())->toBe(2);
})->group('personio');

it('will trigger a sync on table reserve', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $timeOfType = $user->currentTeam->timeOffTypes()->create([
        'name' => 'Home Office',
        'personio_id' => 1,
    ]);

    $table = Table::factory()
        ->state([
            'time_off_type_id' => $timeOfType->id,
        ])
        ->create();

    Queue::fake();

    $this->actingAs($user)
        ->post(route('tables.reservations.store', $table), [
            'date' => today()->format('Y-m-d'),
        ]);

    Queue::assertPushed(SyncReservationToPersonio::class);
})->group('personio');

it('will trigger a sync on table reservation cancel', function () {
    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $timeOfType = $user->currentTeam->timeOffTypes()->create([
        'name' => 'Home Office',
        'personio_id' => 1,
    ]);

    $table = Table::factory()
        ->state([
            'time_off_type_id' => $timeOfType->id,
        ])
        ->create();

    $reservation = Reservation::factory()
        ->state([
            'table_id' => $table->id,
            'user_id' => $user->id,
        ])
        ->create();

    Queue::fake();

    $this->actingAs($user)
        ->delete(route('tables.reservations.destroy', [$table, $reservation]));

    Queue::assertPushed(SyncReservationDeleteToPersonio::class);
})->group('personio');

it('will sync a reservation to personio', function () {
    Http::preventStrayRequests();

    $timeOffPeriodId = 1;

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),

        config('personio.base_url').'/*' => Http::response([
            'success' => true,
            'data' => [
                'type' => 'TimeOffPeriod',
                'attributes' => [
                    'id' => $timeOffPeriodId,
                    'status' => 'approved',
                    'comment' => 'Comment',
                    'start_date' => '2023-06-13T00:00:00+02:00',
                    'end_date' => '2023-06-13T00:00:00+02:00',
                    'days_count' => 1,
                    'half_days_start' => 0,
                    'half_days_end' => 0,
                    'time_off_type' => [
                        'type' => 'TimeOffType',
                        'attributes' => [
                            'id' => 1,
                            'name' => 'Home Office',
                            'category' => 'offsite_work',
                        ],
                    ],
                    'employee' => [
                        'type' => 'Employee',
                        'attributes' => [
                            'id' => [
                                'label' => 'ID',
                                'value' => 1,
                                'type' => 'integer',
                                'universal_id' => 'id',
                            ],
                            'first_name' => [
                                'label' => 'First name',
                                'value' => 'John',
                                'type' => 'string',
                                'universal_id' => 'first_name',
                            ],
                            'last_name' => [
                                'label' => 'Last name',
                                'value' => 'Doe',
                                'type' => 'string',
                                'universal_id' => 'last_name',
                            ],
                            'email' => [
                                'label' => 'Email',
                                'value' => '',
                                'type' => 'string',
                                'universal_id' => 'email',
                            ],
                        ],
                    ],
                    'created_by' => 'API',
                    'certificate' => [
                        'status' => 'not-required',
                    ],
                    'created_at' => '2023-06-12T23:22:48+02:00',
                    'updated_at' => '2023-06-12T23:22:48+02:00',
                ],
            ],
        ]),
    ]);

    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $timeOfType = $user->currentTeam->timeOffTypes()->create([
        'name' => 'Home Office',
        'personio_id' => 1,
    ]);

    $table = Table::factory()
        ->has(Room::factory()->state([
            'team_id' => $user->currentTeam->id,
        ]))
        ->state([
            'time_off_type_id' => $timeOfType->id,
        ])
        ->create();

    $reservation = Reservation::factory()
        ->state([
            'table_id' => $table->id,
            'user_id' => $user->id,
        ])
        ->create();

    $personioService = new PersonioService($user->currentTeam);

    $personioService->syncReservation($reservation);

    expect($reservation->fresh()->personio_id)->toBe($timeOffPeriodId);
})->group('personio');

it('will sync a reservation cancel to personio', function () {
    Http::preventStrayRequests();

    $timeOffPeriodId = 1;

    Http::fake([
        config('personio.base_url').'/auth' => Http::response([
            'success' => true,
            'data' => [
                'token' => Str::random(20),
            ],
        ]),

        config('personio.base_url').'/*' => Http::response([
            'success' => true,
            'data' => [
                'message' => 'The absence period was deleted.',
            ],
        ]),
    ]);

    $user = User::factory()
        ->withPersonalTeam()
        ->create();

    $timeOfType = $user->currentTeam->timeOffTypes()->create([
        'name' => 'Home Office',
        'personio_id' => 1,
    ]);

    $table = Table::factory()
        ->has(Room::factory()->state([
            'team_id' => $user->currentTeam->id,
        ]))
        ->state([
            'time_off_type_id' => $timeOfType->id,
        ])
        ->create();

    $reservation = Reservation::factory()
        ->state([
            'table_id' => $table->id,
            'user_id' => $user->id,
            'personio_id' => $timeOffPeriodId,
        ])
        ->create();

    $personioService = new PersonioService($user->currentTeam);

    $personioService->deleteReservation($reservation);

    expect($reservation->fresh()->personio_id)->toBeNull();
})->group('personio');

<?php

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Client\Request;

it('will push for each notification a job', function () {
    Queue::fake();

    $notification = Notification::factory()->create([
        'type' => NotificationTypeEnum::EMPTY,
    ]);

    $notification->rooms()->attach(
        Room::factory()
            ->hasTables(3)
            ->create()
    );

    $notificationService = new NotificationService();

    $notificationService->handle();

    Queue::assertPushed(SendNotificationJob::class, 1);
})->group('notification');

it('will only push a notification if the condition empty is met', function () {
    Queue::fake();

    $notification = Notification::factory()->create([
        'type' => NotificationTypeEnum::EMPTY,
    ]);

    $notification->rooms()->attach(
        Room::factory()
            ->hasTables(3)
            ->create()
    );

    Reservation::create([
        'user_id' => User::factory()->create()->id,
        'table_id' => $notification->rooms->first()->tables->first()->id,
        'date' => today(),
    ]);

    $notificationService = new NotificationService();

    $notificationService->handle();

    Queue::assertPushed(SendNotificationJob::class, 0);
})->group('notification');

it('will only push a notification if the condition empty is met with a reservation on another day', function () {
    Queue::fake();

    $notification = Notification::factory()->create([
        'type' => NotificationTypeEnum::EMPTY,
    ]);

    $notification->rooms()->attach(
        Room::factory()
            ->hasTables(3)
            ->create()
    );

    Reservation::create([
        'user_id' => User::factory()->create()->id,
        'table_id' => $notification->rooms->first()->tables->first()->id,
        'date' => today()->addDay(),
    ]);

    $notificationService = new NotificationService();

    $notificationService->handle();

    Queue::assertPushed(SendNotificationJob::class, 1);
})->group('notification');

it('will only push a notification if the condition less than is met', function () {
    Queue::fake();

    $notification = Notification::factory()->create([
        'type' => NotificationTypeEnum::LESS_THAN,
        'number' => 2,
    ]);

    $notification->rooms()->attach(
        Room::factory()
            ->hasTables(3)
            ->create()
    );

    Reservation::create([
        'user_id' => User::factory()->create()->id,
        'table_id' => $notification->rooms->first()->tables->first()->id,
        'date' => today(),
    ]);

    $notificationService = new NotificationService();

    $notificationService->handle();

    Queue::assertPushed(SendNotificationJob::class, 1);
})->group('notification');

it('will only push a notification if the condition more than is met', function () {
    Queue::fake();

    $notification = Notification::factory()->create([
        'type' => NotificationTypeEnum::MORE_THAN,
        'number' => 1,
    ]);

    $notification->rooms()->attach(
        Room::factory()
            ->hasTables(3)
            ->create()
    );

    Reservation::create([
        'user_id' => User::factory()->create()->id,
        'table_id' => $notification->rooms->first()->tables->first()->id,
        'date' => today(),
    ]);

    Reservation::create([
        'user_id' => User::factory()->create()->id,
        'table_id' => $notification->rooms->first()->tables->last()->id,
        'date' => today(),
    ]);

    $notificationService = new NotificationService();

    $notificationService->handle();

    Queue::assertPushed(SendNotificationJob::class, 1);
})->group('notification');


it('can send messages via slack', function () {
    Http::preventStrayRequests();

    $slackWebhook = 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX';

    Http::fake([
        $slackWebhook => Http::response('ok'),
    ]);

    $notification = Notification::factory()->create([
        'channel' => NotificationChannelEnum::SLACK,
        'receiver' => $slackWebhook,
        'message' => 'Test',
    ]);

    $notificationService = new NotificationService();

    $notificationService->sendMessage($notification);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX'
            && $request['text'] === 'Test';
    });
})->group('notification');

it('can send messages via email', function () {
    $email = '';

    $notification = Notification::factory()->create([
        'channel' => NotificationChannelEnum::EMAIL,
        'receiver' => $email,
        'message' => 'Test',
    ]);

    $notificationService = new NotificationService();

    $notificationService->sendMessage($notification);
})->group('notification')->skip('Not implemented yet');

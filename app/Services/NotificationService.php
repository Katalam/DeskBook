<?php

namespace App\Services;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\Room;
use App\Models\Table;
use Exception;
use Http;
use Illuminate\Support\Collection;
use Str;

class NotificationService
{
    public function handle(): void
    {
        Notification::query()
            ->with(['rooms.tables.reservations' /*, 'tables.reservations' */])
            ->chunk(100, function (Collection $notifications) {
                $notifications->each(function (Notification $notification) {
                    if ($this->conditionMet($notification)) {
                        SendNotificationJob::dispatch($notification);
                    }
                });
            });
    }

    /**
     * @throws Exception
     */
    public function conditionMet(Notification $notification): bool
    {
        return match ($notification->type) {
            NotificationTypeEnum::EMPTY => $this->conditionEmptyMet($notification),
            NotificationTypeEnum::LESS_THAN => $this->conditionLessThanMet($notification),
            NotificationTypeEnum::MORE_THAN => $this->conditionMoreThanMet($notification),
            default => throw new Exception('Unknown notification type'),
        };
    }

    private function conditionEmptyMet(Notification $notification): bool
    {
        return $notification->rooms->every(function (Room $room) {
            return $room->tables->every(function (Table $table) {
                return $table->reservations->where('date', today())->isEmpty();
            });
        });
    }

    private function conditionLessThanMet(Notification $notification): bool
    {
        return $notification->rooms->every(function (Room $room) use ($notification) {
            return $room->tables->pluck('reservations')
                ->flatten()
                ->where('date', today())->count() < $notification->number;
        });
    }

    private function conditionMoreThanMet(Notification $notification): bool
    {
        return $notification->rooms->every(function (Room $room) use ($notification) {
            return $room->tables->pluck('reservations')
                ->flatten()
                ->where('date', today())->count() > $notification->number;
        });
    }

    /**
     * @throws Exception
     */
    public function sendMessage(Notification $notification): void
    {
        if (! $notification->receiver || ! $notification->message) {
            return;
        }

        match ($notification->channel) {
            NotificationChannelEnum::SLACK => $this->sendMessageViaSlack($notification),
            NotificationChannelEnum::EMAIL => $this->sendMessageViaEmail($notification),
            default => throw new Exception('Unknown notification channel'),
        };
    }

    private function sendMessageViaSlack(Notification $notification): void
    {

        Http::withHeaders([
            'Content-type' => 'application/json',
        ])->post($notification->receiver, [
            'text' => $this->replacePlaceholderInMessage($notification),
        ]);
    }

    private function sendMessageViaEmail(Notification $notification): void
    {

    }

    private function replacePlaceholderInMessage(Notification $notification): string
    {
        $roomNames = $notification->rooms->pluck('name')->implode(', ');
        return Str::of($notification->message)
            ->replace(Notification::PLACEHOLDER, $roomNames)
            ->toString();
    }
}

<?php

namespace App\Services;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\Room;
use App\Models\Table;
use Arr;
use Carbon\CarbonInterface;
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
                    if (! $this->shouldSendToday($notification)) {
                        return;
                    }

                    if ($this->conditionMet($notification)) {
                        SendNotificationJob::dispatch($notification);
                    }
                });
            });
    }

    private function shouldSendToday(Notification $notification): bool
    {
        return Arr::has($notification->days, today()->dayOfWeek);
    }

    /**
     * Returns an array of all weekdays with the weekday number as key and the weekday name as value.
     *
     * @return string[]
     */
    public static function getAllWeekdays(): array
    {
        return [
            CarbonInterface::MONDAY => 'Monday',
            CarbonInterface::TUESDAY => 'Tuesday',
            CarbonInterface::WEDNESDAY => 'Wednesday',
            CarbonInterface::THURSDAY => 'Thursday',
            CarbonInterface::FRIDAY => 'Friday',
            CarbonInterface::SATURDAY => 'Saturday',
            CarbonInterface::SUNDAY => 'Sunday',
        ];
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
                return $table->reservations->where('date', today()->addDay())->isEmpty();
            });
        });
    }

    private function conditionLessThanMet(Notification $notification): bool
    {
        return $notification->rooms->every(function (Room $room) use ($notification) {
            return $room->tables->pluck('reservations')
                ->flatten()
                ->where('date', today()->addDay())->count() < $notification->number;
        });
    }

    private function conditionMoreThanMet(Notification $notification): bool
    {
        return $notification->rooms->every(function (Room $room) use ($notification) {
            return $room->tables->pluck('reservations')
                ->flatten()
                ->where('date', today()->addDay())->count() > $notification->number;
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

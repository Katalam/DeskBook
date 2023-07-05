<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\NotificationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Notification $notification)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle(NotificationService $notificationService): void
    {
        $notificationService->sendMessage($this->notification);
    }
}

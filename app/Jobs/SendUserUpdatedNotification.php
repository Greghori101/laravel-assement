<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendUserUpdatedNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        User::chunkById(1000, function ($users) {
            Notification::send($users, new UserUpdated($this->user));
        });
    }
}

<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Notifications\Notification;

class SMSGistChannel
{
    public function send($notifiable, Notification $notification)
    {
//        if (method_exists($notifiable, 'routeNotificationForSMSGist')) {
//            $to = $notifiable->routeNotificationForSMSGist($notifiable);
//        }

        $message = $notification->toSMSGist($notifiable);
        // Send notification to the $notifiable instance...
        $message->send($notifiable,$notification);
        // Or use dryRun() for testing to send it, without sending it for real.
//        $message->dryRun()->send();

    }
}

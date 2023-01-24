<?php

namespace App\Notifications;

use App\Broadcasting\SMSGistChannel;
use App\Services\SMSGist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendGeneralSMSNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        //
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SMSGistChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SMSGist
     */
    public function toSMSGist($notifiable)
    {
        return (new SMSGist)
            ->msg($this->message);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

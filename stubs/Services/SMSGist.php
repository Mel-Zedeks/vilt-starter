<?php

namespace App\Services;

use App\Broadcasting\SMSGistChannel;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SMSGist
{
    private $api_token;
    private $api_url;
    protected $from;
    protected $to;
    protected $msg;
    protected string $dryrun = 'no';

    public function __construct()
    {
        $this->api_url = config('smsgist.url');
        $this->api_token = config('smsgist.token');
        $this->from = substr(config('smsgist.sender_name'), 0, 11);
    }

    public static function message($recipient, $message, $sender = null)
    {
        return static::apiCall([
            "msisdn" => static::processRecipient($recipient),
            "sender" => $sender ? substr($sender, 0, 11) : substr(config('smsgist.sender_name'), 0, 11),
            "message" => $message,
        ]);
    }


    private static function apiCall(array $data)
    {

        $that = (new self());
        $url = $that->api_url;
        try {
            $request = Http::withToken($that->api_token)->timeout(120)->acceptJson()->post($url, $data)->json();
        } catch (\Exception $exception) {
            report($exception);
            throw ValidationException::withMessages([
                'smsgist' => 'Something went wrong'
            ]);
        }
        return $request;
    }

    private static function processRecipient($recipient)
    {
        return is_array($recipient) ? implode(",", $recipient) : $recipient;
    }

    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    public function to($to)
    {

        $this->to = $to;
        return $this;
    }

    public function msg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    public function dryrun($dry = 'yes'): self
    {
        $this->dryrun = $dry;

        return $this;
    }

    public function send($notifiable, Notification $notification)
    {

        if (!$this->to) {
            $this->to = $notifiable instanceof AnonymousNotifiable
                ? $notifiable->routeNotificationFor(SMSGistChannel::class)
                : $notifiable->phone;
        }

        return self::message($this->to, $this->msg, $this->from);
    }
}

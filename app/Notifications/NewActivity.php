<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewActivity extends Notification
{
    use Queueable;

    public $message;
    public $url;
    public $type;

    public function __construct($message, $url, $type = 'info')
    {
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database']; // Force database storage
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type
        ];
    }
}
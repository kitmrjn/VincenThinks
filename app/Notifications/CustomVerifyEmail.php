<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Generate the 6-digit code right before sending the email
        $notifiable->generateOtp();

        return (new MailMessage)
            ->subject('Verify Your Email Address - VincenThinks')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your identity document was verified successfully! To complete your registration, please verify your email address.')
            ->line('Here is your 6-digit verification code:')
            ->line(new HtmlString('<div style="text-align: center; margin: 20px 0;"><span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #800000;">' . $notifiable->email_verification_code . '</span></div>'))
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not create an account, no further action is required.');
    }
}
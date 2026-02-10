<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail; // Inherit from Laravel's base class

class CustomVerifyEmail extends VerifyEmail
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Generate the verification URL using the parent class's logic
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Action Required: Verify Your VincenThinks Account')
            ->greeting('Hello ' . $notifiable->name . ',') // Personalization
            ->line('Welcome to VincenThinks! We are excited to have you on board.')
            ->line('To ensure the security of your account and access all features, please verify your email address by clicking the button below.')
            ->action('Verify My Account', $verificationUrl)
            ->line('If you did not sign up for an account, no further action is required.')
            ->salutation('Best regards,')
            ->salutation('The VincenThinks Team');
    }
}
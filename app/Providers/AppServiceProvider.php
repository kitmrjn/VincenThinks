<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View; // <--- ADDED THIS IMPORT
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Keep your existing Pagination style
        Paginator::useTailwind();

        // 2. Add Dynamic Email Configuration & Global Settings
        if (Schema::hasTable('settings')) {
            
            // Get all settings in one query (Added 'verification_required')
            $settings = Setting::whereIn('key', [
                'mail_mailer', 'mail_host', 'mail_port', 
                'mail_username', 'mail_password', 'mail_encryption', 
                'mail_from_address', 'mail_from_name',
                'verification_required' // <--- Added this key
            ])->pluck('value', 'key');

            // A. Configure Mail
            if ($settings->isNotEmpty()) {
                Config::set('mail.default', $settings['mail_mailer'] ?? 'smtp');
                
                if (isset($settings['mail_host'])) Config::set('mail.mailers.smtp.host', $settings['mail_host']);
                if (isset($settings['mail_port'])) Config::set('mail.mailers.smtp.port', $settings['mail_port']);
                if (isset($settings['mail_username'])) Config::set('mail.mailers.smtp.username', $settings['mail_username']);
                if (isset($settings['mail_password'])) Config::set('mail.mailers.smtp.password', $settings['mail_password']);
                if (isset($settings['mail_encryption'])) Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption']);
                if (isset($settings['mail_from_address'])) Config::set('mail.from.address', $settings['mail_from_address']);
                if (isset($settings['mail_from_name'])) Config::set('mail.from.name', $settings['mail_from_name']);
            
                // B. Share Verification Setting Globally
                // If setting is '1', variable is true. Otherwise false.
                $isReq = isset($settings['verification_required']) && $settings['verification_required'] == '1';
                View::share('verification_required', $isReq);
            } else {
                View::share('verification_required', false);
            }
        } else {
            // Fallback if table doesn't exist yet
            View::share('verification_required', false);
        }
    }
}
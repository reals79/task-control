<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // Override the email notification for verifying email
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $mail = new MailMessage;
            $mail->subject(__('app.verify_email'));
            $mail->markdown('emails.verify-email', ['url' => $url]);
            return $mail;
        });

        // Override the email notification for reseting password
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()], false));
            $mail = new MailMessage;
            $mail->subject(__('app.reset_password_notification'));
            $mail->markdown('emails.reset-password', ['url' => $url]);
            return $mail;
        });
    }
}

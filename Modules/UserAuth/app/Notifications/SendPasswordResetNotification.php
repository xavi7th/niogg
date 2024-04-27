<?php

namespace Modules\UserAuth\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendPasswordResetNotification extends Notification implements ShouldQueue
{
  use Queueable;

  protected $token;

  public function __construct($token)
  {
    $this->afterCommit()->onQueue('high');

    $this->token = $token;
  }

  public function via()
  {
    return ['mail'];
  }

  public function toMail(User $user): MailMessage
  {
    $url = url(route('auth.password.reset', [
      'token' => $this->token,
      'email' => $user->getEmailForPasswordReset(),
    ]));

    return (new MailMessage())
        ->subject(Lang::get('Reset Password Link'))
        ->greeting('Hello ' . $user->first_name . '.')
        ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
        ->action(Lang::get('Reset Password'), $url)
        ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.users.expire')]))
        ->line(Lang::get('If you did not request a password reset, no further action is required.'))
        ->with('Regards,')
        ->salutation(config('app.name'));
  }
}

<?php

namespace Modules\UserAuth\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StaffAccountCreated extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(private readonly string $password)
  {
    $this->afterCommit()->onQueue('high');
  }

  public function via(object $notifiable): array
  {
    return ['mail'];
  }

  public function toMail(User $staff): MailMessage
  {
    return (new MailMessage())
        ->success()
        ->subject('Staff Account Created')
        ->greeting('Hello ' . $staff->first_name . ',')
        ->line(new HtmlString(
            'A staff account has been created for you on ' . config('app.name') . '. '
            . 'Your password is: <b>' . e($this->password) . '</b>'
        ))
        ->line('You can log in to your dashboard using the button below.')
        ->action('Log In', route('auth.login'))
        ->line('For security reasons, please change your password after your first login.')
        ->salutation(config('app.name'));
  }
}

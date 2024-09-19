<?php

namespace Modules\UserAuth\Notifications;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StaffAccountCreated extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(private string $password)
  {
  }

  public function via()
  {
    return ['mail'];
  }

  public function toMail(User $staff): MailMessage
  {
    return (new MailMessage())
        ->success()
        ->greeting('Hello ' . Str::before($staff->name, ' ') . ',')
        ->line(new HtmlString(
            'A staff account has been created for you on ' . config('app.name') . '. The password to the account is <b><i>' . $this->password . '</i></b>. You can login by clicking the button below.'
        ))
        ->action('Login', route('auth.login'));
  }
}

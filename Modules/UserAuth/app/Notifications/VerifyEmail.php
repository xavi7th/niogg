<?php

namespace Modules\UserAuth\Notifications;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class VerifyEmail extends BaseVerifyEmail
{
  /**
   * Get the verification URL for the given notifiable.
   *
   * @param  User  $notifiable
   */
  protected function verificationUrl($notifiable): string
  {
    if (static::$createUrlCallback) {
      return call_user_func(static::$createUrlCallback, $notifiable);
    }

    return URL::temporarySignedRoute(
        'auth.verification.verify',
        Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
        [
          'id' => $notifiable->getKey(),
          'hash' => sha1($notifiable->getEmailForVerification()),
        ]
    );
  }
}

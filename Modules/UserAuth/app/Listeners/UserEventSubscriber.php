<?php

namespace Modules\UserAuth\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\UserAuth\Notifications\SendPasswordResetSuccessfulNotification;

class UserEventSubscriber implements ShouldQueue
{
  use Queueable;

  public function __construct()
  {
    $this->afterCommit();
  }

  public static function onRegistered(Registered $event): void
  {
    if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
      $event->user->sendEmailVerificationNotification();
    }
  }

  public static function onPasswordReset(PasswordReset $event): void
  {
    $event->user->notify(new SendPasswordResetSuccessfulNotification());
  }

  public function subscribe()
  {
    return [
      Registered::class => 'onRegistered',
      PasswordReset::class => 'onPasswordReset',
    ];
  }
}

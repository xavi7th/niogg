<?php

namespace Modules\UserAuth\Providers;

use Modules\UserAuth\Listeners\UserEventSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class UserAuthEventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [];

  /**
   * The subscriber classes to register.
   *
   * @var array
   */
  protected $subscribe = [
    UserEventSubscriber::class,
  ];

  public function shouldDiscoverEvents(): bool
  {
    return FALSE;
  }
}

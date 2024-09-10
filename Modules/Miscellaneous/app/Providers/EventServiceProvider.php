<?php

namespace Modules\Miscellaneous\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
  protected $listen = [];

  protected static $shouldDiscoverEvents = TRUE;
}

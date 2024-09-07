<?php

namespace Modules\Conference\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
  protected $listen = [];

  protected static $shouldDiscoverEvents = TRUE;
}

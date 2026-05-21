<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
  public const HOME = '/user/dashboard';

  /**
   * Define your route model bindings, pattern filters, and other route configuration.
   */
  public function boot(): void
  {
    parent::boot();
  }

  public static function home(): string
  {
    return static::HOME;
  }
}

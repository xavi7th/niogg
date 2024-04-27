<?php

namespace Modules\AppUser\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
  /**
   * Called before routes are registered.
   *
   * Register any model bindings or pattern based filters.
   */
  public function boot(): void
  {
    parent::boot();
  }

  public function map(): void
  {
    $this->mapApiRoutes();

    $this->mapWebRoutes();
  }

  protected function mapWebRoutes(): void
  {
    Route::middleware('web')->group(module_path('AppUser', '/routes/web.php'));
  }

  protected function mapApiRoutes(): void
  {
    Route::middleware('api')->prefix('api')->name('api.')->group(module_path('AppUser', '/routes/api.php'));
  }
}

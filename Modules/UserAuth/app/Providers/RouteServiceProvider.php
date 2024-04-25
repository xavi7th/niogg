<?php

namespace Modules\UserAuth\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    parent::boot();
  }

  public function map(): void
  {
    $this->mapWebRoutes();
  }

  protected function mapWebRoutes(): void
  {
    Route::middleware('web')->group(module_path('UserAuth', '/routes/web.php'));
  }
}

<?php

namespace Modules\PublicPage\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

    $this->routes(function (): void {
      Route::middleware('web')->group(module_path('PublicPage', '/routes/web.php'));
    });
  }
}

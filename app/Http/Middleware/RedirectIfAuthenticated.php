<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
  /**
   * Handle an incoming request.
   *
   * @param  Closure(Request): (Response)  $next
   */
  public function handle(Request $request, Closure $next, string ...$guards): Response
  {
    $guards = empty($guards) ? [NULL] : $guards;

    foreach ($guards as $guard) {
      if (Auth::guard($guard)->check()) {
        $location = Auth::guard($guard)->user()->is_admin ? route('admin.dashboard') : route('appuser.dashboard');
        return redirect($location);
      }
    }

    return $next($request);
  }
}

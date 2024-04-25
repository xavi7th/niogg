<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HandleInertiaRequests extends Middleware
{
  protected $rootView = 'app';

  /**
   * Determine the current asset version.
   */
  public function version(Request $request): ?string
  {
    return parent::version($request);
  }

  /**
   * Define the props that are shared by default.
   *
   * @return array<string, mixed>
   */
  public function share(Request $request): array
  {
    return [
      ...parent::share($request),
      'auth' => [
        'user' => $request->user(),
      ],
    ];
  }

  public function rootView(Request $request): string
  {
    if (is_null(Route::currentRouteName())) {
      return $this->rootView;
    }

    if (Str::startsWith(Route::currentRouteName(), 'auth.')) {
      return 'userauth::app';
    }

    dd('Define a logic for loading required module page');

    return Str::before(Route::currentRouteName(), '.') . '::app';
  }
}

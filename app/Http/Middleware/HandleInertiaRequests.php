<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class HandleInertiaRequests extends Middleware
{
  protected $rootView = 'publicpage::app';

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
      'app' => fn () => [
        'name' => config('app.name'),
        'email' => config('app.email'),
        'alt_name' => config('app.alt_name'),
        'alt_email' => config('app.alt_email'),
        'phone' => config('app.phone'),
        'alt_phone' => config('app.alt_phone'),
        'address' => config('app.address'),
      ],
      'flash' => fn () => Session::get('flash') ?? (object) [],
      'isInertiaRequest' => (bool) request()->header('X-Inertia'),
      'auth' => [
        'user' => $request->user(),
      ],
    ];
  }

  public function resolveValidationErrors(Request $request): object
  {
    if ( ! $request->session()->has('errors') && ! $request->session()->has('flash.error')) {
      return (object) [];
    }

    if ($request->session()->has('errors')) {
      return (object) collect($request->session()->get('errors')->getBags())->map(fn ($bag) => (object) collect($bag->messages())->map(fn ($errors) => $errors[0])->toArray())->pipe(function ($bags) use ($request) {
        if ($bags->has('default') && $request->header('x-inertia-error-bag')) {
          return [$request->header('x-inertia-error-bag') => $bags->get('default')];
        }
        if ($bags->has('default')) {
          return $bags->get('default');
        }

          return $bags->toArray();
      });
    } elseif ($request->session()->has('flash.error')) {
      return (object) $request->session()->get('flash');
    }
  }

  public function rootView(Request $request): string
  {
    if (is_null(Route::currentRouteName()) || Str::startsWith(Route::currentRouteName(), 'app.')) {
      return $this->rootView;
    }

    if (Str::startsWith(Route::currentRouteName(), 'auth.')) {
      return 'userauth::app';
    }

    if ($request->user()) {
      return mb_strtolower($request->user()->getType()) . '::app';
    }

    return Str::before(Route::currentRouteName(), '.') . '::app';
  }
}

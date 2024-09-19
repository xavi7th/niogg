<?php

namespace Modules\UserAuth\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;
use Modules\UserAuth\Http\Requests\LoginRequest;

class AuthenticatedSessionController extends Controller
{
  public function create(): Response
  {
    return Inertia::render('UserAuth::Login', [
      'canResetPassword' => Route::has('auth.password.request'),
      'title' => 'Login to ' . config('app.name') . ' Admin Area',
    ])->withViewData([
      'pageTitle' => 'Login to ' . config('app.name') . ' Admin Area',
      'metaDesc' => 'Login to access Admin dashboard and manage conference registrants',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  /**
   * Handle an incoming authentication request.
   */
  public function store(LoginRequest $request): RedirectResponse
  {
    $request->authenticate();

    $request->session()->regenerate();

    return Inertia::location(RouteServiceProvider::home());
  }

  /**
   * Destroy an authenticated session.
   */
  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return Inertia::location(redirect('/'));
  }
}

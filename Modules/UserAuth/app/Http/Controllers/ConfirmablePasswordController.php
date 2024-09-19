<?php

namespace Modules\UserAuth\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
  public function show(): Response
  {
    return Inertia::render('UserAuth::ConfirmPassword')->withViewData([
      'pageTitle' => 'Confirm Password',
      'metaDesc' => 'Confirm your password to authorize this action',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  public function store(Request $request): RedirectResponse
  {
    if ( ! Auth::guard('web')->validate(['email' => $request->user()->email, 'password' => $request->password])) {
      throw ValidationException::withMessages([
        'password' => __('auth.password'),
      ]);
    }

    $request->session()->put('auth.password_confirmed_at', time());

    return redirect()->intended(RouteServiceProvider::home());
  }
}

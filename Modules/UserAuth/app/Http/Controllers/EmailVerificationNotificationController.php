<?php

namespace Modules\UserAuth\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;

class EmailVerificationNotificationController extends Controller
{
  /**
   * Send a new email verification notification.
   */
  public function store(Request $request): RedirectResponse
  {
    /** @var User */
    $user = $request->user();

    if ($user->hasVerifiedEmail()) {
      return redirect()->intended(RouteServiceProvider::home());
    }

    $user->sendEmailVerificationNotification();

    return back()->withFlash(['success' => 'Verification link sent']);
  }
}

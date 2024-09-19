<?php

namespace Modules\UserAuth\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
  /**
   * Display the password reset link request view.
   */
  public function create(): Response
  {
    return Inertia::render('UserAuth::ForgotPassword', [
      'status' => session('status'),
    ])->withViewData([
      'pageTitle' => 'Password Reset',
      'metaDesc' => 'Request a password reset link',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  /**
   * Handle an incoming password reset link request.
   *
   * @throws ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'email' => 'required|email',
    ]);

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_LINK_SENT) {
      return back()->withFlash(['success' => __($status)]);
    }

    throw ValidationException::withMessages([
      'email' => [trans($status)],
    ]);
  }
}

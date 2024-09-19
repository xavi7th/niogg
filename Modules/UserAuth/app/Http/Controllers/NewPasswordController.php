<?php

namespace Modules\UserAuth\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
  /**
   * Display the password reset view.
   */
  public function create(Request $request): Response
  {
    return Inertia::render('UserAuth::ResetPassword', [
      'email' => $request->email,
      'token' => $request->route('token'),
    ])->withViewData([
      'pageTitle' => 'Reset Your Password',
      'metaDesc' => 'Create a new password for your account.',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  /**
   * Handle an incoming new password request.
   *
   * @throws ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Here we will attempt to reset the user's password. If it is successful we
    // will update the password on an actual user model and persist it to the
    // database. Otherwise we will parse the error and return the response.
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request): void {
          $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
          ])->save();

          event(new PasswordReset($user));
        }
    );

    // If the password was successfully reset, we will redirect the user back to
    // the application's home authenticated view. If there is an error we can
    // redirect them back to where they came from with their error message.
    if ($status === Password::PASSWORD_RESET) {
      return redirect()->route('auth.login')->withFlash(['success' => __($status)]);
    }

    throw ValidationException::withMessages([
      'email' => [trans($status)],
    ]);
  }
}

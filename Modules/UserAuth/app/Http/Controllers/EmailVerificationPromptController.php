<?php

namespace Modules\UserAuth\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;

class EmailVerificationPromptController extends Controller
{
  /**
   * Display the email verification prompt.
   */
  public function __invoke(Request $request): RedirectResponse|Response
  {
    return $request->user()->hasVerifiedEmail()
      ? redirect()->intended(RouteServiceProvider::home())
      : Inertia::render('UserAuth::VerifyEmail')->withViewData([
        'title' => 'Email Verification',
        'metaDesc' => 'Kindly verify your email to access your dashboard. Remember to check your spam mail, if you can\'t find our mail in your inbox',
        'ogUrl' => route('app.index'),
        'canonical' => route('app.index'),
      ]);
  }
}

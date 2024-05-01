<?php

namespace Modules\AppUser\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Modules\AppUser\Http\Requests\ProfileUpdateRequest;

class AppUserProfileController extends Controller
{
  public function edit(Request $request): Response
  {
    /** @var User */
    $user = $request->user();

    return Inertia::render('AppUser::Profile/Edit', [
      'must_verify_email' => $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail(),
    ])->withViewData([
      'title' => 'Profile',
      'metaDesc' => 'View your profile page',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  public function update(ProfileUpdateRequest $request): RedirectResponse
  {
    $request->user()->fill($request->validated());

    if ($request->user()->isDirty('email')) {
      $request->user()->email_verified_at = NULL;
    }

    $request->user()->save();

    return to_route('appuser.profile.edit');
  }

  public function destroy(Request $request): RedirectResponse
  {
    $request->validate([
      'password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Inertia::location(route('app.index'));
  }
}

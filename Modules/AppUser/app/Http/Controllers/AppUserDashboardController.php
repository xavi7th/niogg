<?php

namespace Modules\AppUser\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;

class AppUserDashboardController extends Controller
{
  public function __invoke(): Response
  {
    // $this->authorize('index', AppUser::class);

    return Inertia::render('AppUser::Dashboard', [
      'phpVersion' => PHP_VERSION,
    ])->withViewData([
      'title' => 'User Dashboard',
      'metaDesc' => 'Welsome to your user dashboard area.',
      'ogUrl' => route('auth.login'),
      'canonical' => route('auth.login'),
    ]);
  }
}

<?php

namespace Modules\AppUser\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;

class AppUserDashboardController extends Controller
{
  public function __invoke(): Response
  {
    return Inertia::render('AppUser::Dashboard', [
      'title' => 'Staff Dashboard',
    ])->withViewData([
      'pageTitle' => 'Staff Dashboard',
      'metaDesc' => 'This is where you can manage the site contenet and the conference registrants.',
      'ogUrl' => route('auth.login'),
      'canonical' => route('auth.login'),
    ]);
  }
}

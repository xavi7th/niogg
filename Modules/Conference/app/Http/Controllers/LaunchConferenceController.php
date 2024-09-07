<?php

namespace Modules\Conference\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;

class LaunchConferenceController extends Controller
{
  public function index()
  {
    return Inertia::render('Conference::LaunchConference', [
      'pageTitle' => 'Welcome to ' . config('app.name'),
      'testimonials' => [],
    ])->withViewData([
      'pageTitle' => 'About ' . config('app.name'),
      'metaDesc' => 'At ' . config('app.name') . ' we envision a society where the government is transparent and accountable to her citizens.',
      'ogUrl' => route('app.about'),
      'canonical' => route('app.about'),
    ]);
  }
}

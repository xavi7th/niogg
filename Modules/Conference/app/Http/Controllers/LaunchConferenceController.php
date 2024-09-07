<?php

namespace Modules\Conference\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Vite;

class LaunchConferenceController extends Controller
{
  public function index()
  {
    return Inertia::render('Conference::LaunchConference', [
      'pageTitle' => 'The Nigeria Leadership Quest For For Economic Growth & Business Summit / Gala Night Award by ' . config('app.name'),
      'testimonials' => [],
    ])->withViewData([
      'pageTitle' => 'The Nigeria Leadership Quest For For Economic Growth & Business Summit / Gala Night Award by ' . config('app.name'),
      'metaDesc' => 'At ' . config('app.name') . ' we envision a society where the government is transparent and accountable to her citizens.',
      'ogUrl' => route('app.index'),
      'ogImg' => Vite::asset('niogg-flyer.webp'),
      'canonical' => route('app.index'),
    ]);
  }
}

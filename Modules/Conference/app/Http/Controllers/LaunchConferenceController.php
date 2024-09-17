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
      'metaDesc' => 'This event scheduled for 6th November, 2024 at Eko Hotels and Suites, Lagos, Nigeria, time for the morning session 9:00am to 1:00pm and for the ' .
                    'evening session BUSINESS SUMMIT GALA NIGHT AWARD 6:00pm to 10:00pm. By partnering with us, your Company/Brand can play a significant role in advancing ' .
                    'transparency, accountability, and effective leadership in our nation',
      'ogUrl' => route('app.conferences.launch.index'),
      'ogImg' => Vite::asset('niogg-flyer.webp'),
      'canonical' => route('app.index'),
    ]);
  }
}

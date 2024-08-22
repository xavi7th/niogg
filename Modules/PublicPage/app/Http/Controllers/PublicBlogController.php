<?php

namespace Modules\PublicPage\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;

class PublicBlogController extends Controller
{
  public function index()
  {
    return Inertia::render('PublicPage::BlogIndex', [
      'pageTitle' => 'Welcome to ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Welcome to ' . config('app.name'),
      'metaDesc' => config('app.alt_name') . ' promotes good governance in Nigeria by empowering youth through leadership training, advocating for transparency and accountability in
            public institutions, enhancing the electoral system, and educating citizens on their roles in governance to encourage active participation and societal development.',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }

  public function show()
  {
    return Inertia::render('PublicPage::BlogShow', [
      'pageTitle' => 'Welcome to ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Welcome to ' . config('app.name'),
      'metaDesc' => config('app.alt_name') . ' promotes good governance in Nigeria by empowering youth through leadership training, advocating for transparency and accountability in
            public institutions, enhancing the electoral system, and educating citizens on their roles in governance to encourage active participation and societal development.',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }
}

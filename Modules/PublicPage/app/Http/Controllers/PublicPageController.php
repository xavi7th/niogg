<?php

namespace Modules\PublicPage\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class PublicPageController extends Controller
{
  public function index()
  {
    return Inertia::render('PublicPage::Index', [
      'phpVersion' => PHP_VERSION,
      'canLogin' => Route::has('auth.login'),
      'canRegister' => Route::has('auth.register'),
      'laravelVersion' => Application::VERSION,
      'phpVersion' => PHP_VERSION,
      'pageTitle' => 'Welcome to ' . config('app.name'),
    ])->withViewData([
      'pageTitle' => 'Welcome to ' . config('app.name'),
      'metaDesc' => 'Description of this page',
      'ogUrl' => route('app.index'),
      'canonical' => route('app.index'),
    ]);
  }
}

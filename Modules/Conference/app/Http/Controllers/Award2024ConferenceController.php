<?php

namespace Modules\Conference\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Vite;

class Award2024ConferenceController extends Controller
{
  public function index()
  {
    return Inertia::render('Conference::Award2024Conference/AboutAwards', [
      'pageTitle' => 'Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024 by ' . config('app.name'),
      'testimonials' => [],
    ])->withViewData([
      'pageTitle' => 'Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024 by ' . config('app.name'),
      'metaDesc' => 'This event is scheduled for May 23rd, 2025 is a brilliant opportunity to connect with visionary leaders, policymakers, ' .
                    'industry experts, and youth advocates. By partnering with us, your Company/Brand can play a significant role in advancing ' .
                    'transparency, accountability, and effective leadership in our nation',
      'ogUrl' => route('app.conferences.award.index'),
      'ogImg' => Vite::asset('niogg-flyer.webp'),
      'canonical' => route('app.index'),
    ]);
  }

  public function awardees()
  {
    return Inertia::render('Conference::Award2024Conference/Awardees', [
      'pageTitle' => 'NIOGG Achievers Icon/Merit Awards 2024 – Celebrating Champions of Good Governance',
    ])->withViewData([
      'pageTitle' => 'NIOGG Achievers Icon/Merit Awards 2024 – Celebrating Champions of Good Governance',
      'metaDesc' => 'Get full details on the ' . config('app.name') . ' (NIOGG) Achievers Icon/Merit Awards 2024 — a prestigious conference celebrating individuals ' .
                    'and organizations driving positive change, good governance, and community development across Nigeria. This event is scheduled for May 23rd, 2025 ' .
                    'and is a brilliant opportunity to connect with visionary leaders, policymakers, industry experts, and youth advocates. By partnering with us, your ' .
                    'company/brand can play a significant role in advancing transparency, accountability, and effective leadership in our nation.',
      'ogUrl' => route('app.careers'),
      'canonical' => route('app.careers'),
    ]);
  }
}

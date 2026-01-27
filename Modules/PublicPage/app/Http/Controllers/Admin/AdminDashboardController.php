<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard with stats overview
     */
    public function index(): \Inertia\Response
    {
        $stats = [
            'total_events' => Event::count(),
            'published_events' => Event::where('is_published', TRUE)->count(),
            'draft_events' => Event::where('is_published', FALSE)->count(),
            'total_videos' => Video::count(),
        ];

        $recentEvents = Event::with('videos')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentEvents' => $recentEvents,
        ]);
    }
}

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
        $now = now();

        $stats = [
            'total_events' => Event::count(),
            'published_events' => Event::where('is_published', TRUE)->count(),
            'draft_events' => Event::where('is_published', FALSE)->count(),
            'total_videos' => Video::count(),
            'featured_videos' => Video::where('is_featured', TRUE)->count(),
            'upcoming_events' => Event::where('event_date', '>=', $now)->count(),
            'past_events' => Event::where('event_date', '<', $now)->count(),
            'storage_used_bytes' => (int) Video::sum('file_size'),
            'avg_videos_per_event' => Event::has('videos')->withCount('videos')->get()->avg('videos_count') ?: 0,
        ];

        // Category breakdown
        $categoryStats = Event::query()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($item) => [
                'category' => $item->category ?? 'Uncategorized',
                'count' => $item->count,
            ])
            ->values();

        $recentEvents = Event::with('videos')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent activity
        $recentActivity = collect();

        $recentEvents->take(3)->each(function ($event) use ($recentActivity): void {
            $recentActivity->push([
                'type' => 'event_created',
                'message' => "Event \"{$event->name}\" was created",
                'time' => $event->created_at->diffForHumans(),
            ]);
        });

        Video::with('event')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->each(function ($video) use ($recentActivity): void {
                $recentActivity->push([
                    'type' => 'video_uploaded',
                    'message' => "Video \"{$video->title}\" was uploaded to \"{$video->event->name}\"",
                    'time' => $video->created_at->diffForHumans(),
                ]);
            });

        return Inertia::render('PublicPage::Admin/Dashboard', [
            'stats' => $stats,
            'categoryStats' => $categoryStats,
            'recentEvents' => $recentEvents,
            'recentActivity' => $recentActivity->sortByDesc('time')->take(6)->values(),
        ]);
    }
}

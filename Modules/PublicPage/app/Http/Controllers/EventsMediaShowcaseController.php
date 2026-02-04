<?php

namespace Modules\PublicPage\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Event;

class EventsMediaShowcaseController extends Controller
{
    /**
     * Display all published events with videos
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'newest');
        $direction = $sort === 'newest' ? 'desc' : 'asc';

        $events = Event::with('videos')
            ->published()
            ->ordered($direction)
            ->get();

        return Inertia::render('PublicPage::EventsMediaShowcase', [
        'events' => $events,
        'pageTitle' => 'Events Media Showcase',
        'sort' => $sort,
        ]);
    }

    /**
     * Display single event with paginated videos
     */
    public function show(Event $event)
    {
        $videos = $event->videos()
            ->ordered()
            ->paginate(12);

        return Inertia::render('PublicPage::EventVideosGrid', [
        'event' => $event,
        'videos' => $videos,
        'pageTitle' => $event->name,
        ]);
    }

    /**
     * Get paginated videos for event-specific grid
     */
    public function eventVideos(Event $event)
    {
        $videos = $event->videos()
            ->ordered()
            ->paginate(12);

        return Inertia::render('PublicPage::EventVideosGrid', [
        'event' => $event,
        'videos' => $videos,
        'pageTitle' => $event->name,
        ]);
    }
}

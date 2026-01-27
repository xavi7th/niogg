<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Event;

class AdminEventController extends Controller
{
    /**
     * Display paginated list of events for admin
     */
    public function index(): \Inertia\Response
    {
        $events = Event::with('videos')
            ->orderBy('event_date', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
        ]);
    }

    /**
     * Display single event with associated videos
     */
    public function show(Event $event): \Inertia\Response
    {
        $event->load(['videos' => function ($query): void {
            $query->orderBy('sort_order')->orderBy('created_at');
        }]);

        return Inertia::render('Admin/Events/Show', [
            'event' => $event,
        ]);
    }
}

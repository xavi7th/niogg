<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Redirect;
use Modules\PublicPage\Http\Requests\Admin\EventFormRequest;

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
     * Show the form for creating a new event
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Admin/Events/Create');
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

    /**
     * Show the form for editing an existing event
     */
    public function edit(Event $event): \Inertia\Response
    {
        return Inertia::render('Admin/Events/Edit', [
            'event' => $event,
        ]);
    }

    /**
     * Store a new event
     */
    public function store(EventFormRequest $request): \Illuminate\Http\RedirectResponse
    {
        Event::create($request->validated());

        return Redirect::route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Update an existing event
     */
    public function update(EventFormRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $event->update($request->validated());

        return Redirect::route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }
}

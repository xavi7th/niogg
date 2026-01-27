<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Modules\PublicPage\Http\Requests\Admin\EventFormRequest;

class AdminEventController extends Controller
{
    /**
     * Display paginated list of events for admin
     */
    public function index(): \Inertia\Response
    {
        $page = request('page', 1);
        $perPage = request('per_page', 15);

        $cacheKey = "admin.events.list:page:{$page}:per_page:{$perPage}";

        $events = Cache::tags(['admin.events'])->remember($cacheKey, 3600, fn () => Event::with('videos')
            ->orderBy('event_date', 'desc')
            ->paginate(15));

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

        Cache::tags(['admin.events'])->flush();

        return Redirect::route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Update an existing event
     */
    public function update(EventFormRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $event->update($request->validated());

        Cache::tags(['admin.events'])->flush();

        return Redirect::route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Delete an event with cascading delete for associated videos
     */
    public function destroy(Event $event): \Illuminate\Http\RedirectResponse
    {
        $event->delete();

        Cache::tags(['admin.events'])->flush();

        return Redirect::route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Bulk publish multiple events
     */
    public function bulkPublish(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'event_ids' => ['required', 'array', 'min:1'],
            'event_ids.*' => ['exists:events,id'],
        ]);

        $count = Event::whereIn('id', $request->event_ids)->update(['is_published' => TRUE]);

        Cache::tags(['admin.events'])->flush();

        return response()->json([
            'message' => "{$count} event(s) published successfully.",
            'count' => $count,
        ]);
    }

    /**
     * Bulk unpublish multiple events
     */
    public function bulkUnpublish(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'event_ids' => ['required', 'array', 'min:1'],
            'event_ids.*' => ['exists:events,id'],
        ]);

        $count = Event::whereIn('id', $request->event_ids)->update(['is_published' => FALSE]);

        Cache::tags(['admin.events'])->flush();

        return response()->json([
            'message' => "{$count} event(s) unpublished successfully.",
            'count' => $count,
        ]);
    }

    /**
     * Bulk delete multiple events with cascading delete for associated videos
     */
    public function bulkDelete(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'event_ids' => ['required', 'array', 'min:1'],
            'event_ids.*' => ['exists:events,id'],
        ]);

        $count = Event::whereIn('id', $request->event_ids)->delete();

        Cache::tags(['admin.events'])->flush();

        return response()->json([
            'message' => "{$count} event(s) deleted successfully.",
            'count' => $count,
        ]);
    }
}

<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Exception;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
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

        $events = Cache::remember($cacheKey, 3600, fn () => Event::with('videos')
            ->orderBy('event_date', 'desc')
            ->paginate(15));

        return Inertia::render('PublicPage::Admin/Events/Index', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new event
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('PublicPage::Admin/Events/Create', [
            'categories' => config('event_categories'),
        ]);
    }

    /**
     * Display single event with associated videos
     */
    public function show(Event $event): \Inertia\Response
    {
        $event->load([
            'videos' => function ($query): void {
                $query->orderBy('sort_order')->orderBy('created_at');
            },
            'photos' => function ($query): void {
                $query->ordered();
            },
        ]);

        return Inertia::render('PublicPage::Admin/Events/Show', [
            'event' => $event,
        ]);
    }

    /**
     * Show the form for editing an existing event
     */
    public function edit(Event $event): \Inertia\Response
    {
        return Inertia::render('PublicPage::Admin/Events/Edit', [
            'categories' => config('event_categories'),
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'icon' => $event->icon,
                'category' => $event->category,
                'event_date' => $event->event_date?->format('Y-m-d'),
                'slug' => $event->slug,
                'is_published' => $event->is_published,
            ],
        ]);
    }

    /**
     * Store a new event
     */
    public function store(EventFormRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            Event::create($request->validated());

            // Clear events list cache
            $this->clearEventsCache();

            return Redirect::route('admin.events.index')
                ->with('success', 'Event created successfully.');
        } catch (Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Failed to create event. Please try again.');
        }
    }

    /**
     * Update an existing event
     */
    public function update(EventFormRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        try {
            $event->update($request->validated());

            // Clear events list cache
            $this->clearEventsCache();

            return Redirect::route('admin.events.index')
                ->with('success', 'Event updated successfully.');
        } catch (Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Failed to update event. Please try again.');
        }
    }

    /**
     * Delete an event with cascading delete for associated videos
     */
    public function destroy(Event $event): \Illuminate\Http\RedirectResponse
    {
        try {
            $event->delete();

            // Clear events list cache
            $this->clearEventsCache();

            return Redirect::route('admin.events.index')
                ->with('success', 'Event deleted successfully.');
        } catch (Exception $e) {
            return Redirect::back()
                ->with('error', 'Failed to delete event. Please try again.');
        }
    }

    /**
     * Bulk publish multiple events
     */
    public function bulkPublish(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'event_ids' => ['required', 'array', 'min:1'],
                'event_ids.*' => ['exists:events,id'],
            ]);

            $count = Event::whereIn('id', $request->event_ids)->update(['is_published' => TRUE]);

            // Clear events list cache
            $this->clearEventsCache();

            return response()->json([
                'message' => "{$count} event(s) published successfully.",
                'count' => $count,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to publish events. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : NULL,
            ], 500);
        }
    }

    /**
     * Bulk unpublish multiple events
     */
    public function bulkUnpublish(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'event_ids' => ['required', 'array', 'min:1'],
                'event_ids.*' => ['exists:events,id'],
            ]);

            $count = Event::whereIn('id', $request->event_ids)->update(['is_published' => FALSE]);

            // Clear events list cache
            $this->clearEventsCache();

            return response()->json([
                'message' => "{$count} event(s) unpublished successfully.",
                'count' => $count,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to unpublish events. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : NULL,
            ], 500);
        }
    }

    /**
     * Bulk delete multiple events with cascading delete for associated videos
     */
    public function bulkDelete(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'event_ids' => ['required', 'array', 'min:1'],
                'event_ids.*' => ['exists:events,id'],
            ]);

            $count = Event::whereIn('id', $request->event_ids)->delete();

            // Clear events list cache
            $this->clearEventsCache();

            return response()->json([
                'message' => "{$count} event(s) deleted successfully.",
                'count' => $count,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete events. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : NULL,
            ], 500);
        }
    }

    /**
     * Clear events list cache for all pages
     */
    protected function clearEventsCache(): void
    {
        // Clear all paginated event list caches
        $perPage = 15;
        $maxPages = 100; // Safety limit

        for ($page = 1; $page <= $maxPages; $page++) {
            Cache::forget("admin.events.list:page:{$page}:per_page:{$perPage}");
        }
    }
}

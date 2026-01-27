<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Redirect;
use Modules\PublicPage\Http\Requests\Admin\VideoFormRequest;

class AdminVideoController extends Controller
{
    /**
     * Display paginated list of videos for an event
     */
    public function index(Event $event): \Inertia\Response
    {
        $videos = $event->videos()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Videos/Index', [
            'event' => $event,
            'videos' => $videos,
        ]);
    }

    /**
     * Store a new video for an event
     */
    public function store(VideoFormRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $video = $event->videos()->create($request->validated());

        return Redirect::route('admin.events.show', $event)
            ->with('success', 'Video added successfully.');
    }

    /**
     * Update an existing video
     */
    public function update(VideoFormRequest $request, Video $video): \Illuminate\Http\RedirectResponse
    {
        $video->update($request->validated());

        return Redirect::route('admin.events.show', $video->event)
            ->with('success', 'Video updated successfully.');
    }

    /**
     * Delete a video
     */
    public function destroy(Video $video): \Illuminate\Http\RedirectResponse
    {
        $event = $video->event;
        $video->delete();

        return Redirect::route('admin.events.show', $event)
            ->with('success', 'Video deleted successfully.');
    }
}

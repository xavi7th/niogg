<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Redirect;
use Modules\PublicPage\Http\Requests\Admin\VideoFormRequest;
use Modules\PublicPage\Http\Requests\Admin\VideoUploadRequest;
use Modules\PublicPage\Services\VideoUploadService;

class AdminVideoController extends Controller
{
    private VideoUploadService $uploadService;

    public function __construct(VideoUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    /**
     * Handle video file upload with chunked upload support
     */
    public function upload(VideoUploadRequest $request, Event $event): \Illuminate\Http\JsonResponse
    {
        $action = $request->input('action');

        try {
            return match ($action) {
                'initialize' => response()->json(
                    $this->uploadService->initializeUpload($request->file('file'), $event->id)
                ),
                'chunk' => response()->json(
                    $this->uploadService->uploadChunk(
                        $request->input('upload_id'),
                        $request->file('chunk'),
                        (int) $request->input('chunk_index'),
                        (int) $request->input('total_chunks')
                    )
                ),
                'finalize' => response()->json([
                    'video' => $this->uploadService->finalizeUpload(
                        $request->input('upload_id'),
                        $request->only(['title', 'description', 'duration_seconds', 'is_featured', 'sort_order'])
                    ),
                ], 201),
                'resume' => response()->json(
                    $this->uploadService->resumeUpload($request->input('upload_id'))
                ),
                'cancel' => response()->json(
                    tap(
                        ['message' => 'Upload cancelled successfully.'],
                        fn() => $this->uploadService->cancelUpload($request->input('upload_id'))
                    )
                ),
                default => response()->json([
                    'message' => 'Invalid action.',
                ], 400),
            };
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during upload.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

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

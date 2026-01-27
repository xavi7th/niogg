<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Exception;
use Inertia\Inertia;
use InvalidArgumentException;
use App\Http\Controllers\Controller;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Redirect;
use Modules\PublicPage\Services\VideoUploadService;
use Modules\PublicPage\Http\Requests\Admin\VideoFormRequest;
use Modules\PublicPage\Http\Requests\Admin\VideoUploadRequest;

class AdminVideoController extends Controller
{
    private VideoUploadService $uploadService;

    public function __construct(VideoUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Show the form for uploading videos to an event
     */
    public function create(Event $event): \Inertia\Response
    {
        return Inertia::render('Admin/Videos/Upload', [
            'event' => $event,
        ]);
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
                        fn () => $this->uploadService->cancelUpload($request->input('upload_id'))
                    )
                ),
                default => response()->json([
                    'message' => 'Invalid action.',
                ], 400),
            };
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred during upload.',
                'error' => config('app.debug') ? $e->getMessage() : NULL,
            ], 500);
        }
    }

    /**
     * Show the form for editing a video
     */
    public function edit(Video $video): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'video' => $video,
        ]);
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

    /**
     * Reorder videos within an event
     */
    public function reorder(\Illuminate\Http\Request $request, Event $event): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'video_ids' => 'required|array',
            'video_ids.*' => 'integer|exists:videos,id',
        ]);

        $videoIds = $request->input('video_ids');

        // Verify all videos belong to this event
        $eventVideoIds = $event->videos()->pluck('id')->toArray();
        $invalidIds = array_diff($videoIds, $eventVideoIds);

        if ( ! empty($invalidIds)) {
            return response()->json([
                'message' => 'Some videos do not belong to this event.',
            ], 400);
        }

        // Update sort_order for each video
        foreach ($videoIds as $index => $videoId) {
            Video::where('id', $videoId)->update(['sort_order' => $index]);
        }

        return response()->json([
            'message' => 'Videos reordered successfully.',
            'order' => $videoIds,
        ]);
    }
}

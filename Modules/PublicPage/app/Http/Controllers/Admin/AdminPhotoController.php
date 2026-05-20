<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
use Modules\PublicPage\Services\EventPhotoUploadService;
use Modules\PublicPage\Http\Requests\Admin\StoreEventPhotosRequest;
use Modules\PublicPage\Http\Requests\Admin\UpdateEventPhotoRequest;

class AdminPhotoController extends Controller
{
    public function __construct(
        private readonly EventPhotoUploadService $photoService,
    ) {
    }

    public function store(StoreEventPhotosRequest $request, Event $event): JsonResponse
    {
        $altTexts = $request->input('alt_texts', []);
        $startOrder = $event->photos()->max('sort_order') + 1;
        $photos = [];

        foreach ($request->file('photos', []) as $index => $file) {
            $photo = $this->photoService->store($file, $event, $startOrder + $index);

            if (isset($altTexts[$index])) {
                $photo->update(['alt_text' => $altTexts[$index]]);
            }

            $photos[] = $photo;
        }

        return response()->json([
          'message' => count($photos) . ' photo(s) uploaded successfully.',
          'photos' => $photos,
        ]);
    }

    public function update(UpdateEventPhotoRequest $request, EventPhoto $photo): JsonResponse
    {
        $photo->update($request->validated());

        return response()->json([
          'message' => 'Photo updated.',
          'photo' => $photo->fresh(),
        ]);
    }

    public function reorder(Request $request, Event $event): JsonResponse
    {
        $request->validate([
          'photo_ids' => ['required', 'array'],
          'photo_ids.*' => ['integer', 'exists:event_photos,id'],
        ]);

        foreach ($request->photo_ids as $index => $photoId) {
            EventPhoto::where('id', $photoId)->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Photos reordered.']);
    }

    public function destroy(EventPhoto $photo): JsonResponse
    {
        $this->photoService->delete($photo);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted.']);
    }

    public function retryThumbnail(EventPhoto $photo): JsonResponse
    {
        if ($photo->thumbnail_url) {
            return response()->json(['message' => 'Thumbnail already exists.']);
        }

        GeneratePhotoThumbnail::dispatch($photo);

        return response()->json(['message' => 'Thumbnail generation queued.']);
    }
}

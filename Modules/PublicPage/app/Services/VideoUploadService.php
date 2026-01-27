<?php

namespace Modules\PublicPage\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\PublicPage\Models\Video;

class VideoUploadService
{
    private const MAX_FILE_SIZE = 1073741824; // 1GB in bytes
    private const ALLOWED_MIME_TYPES = [
        'video/mp4',
        'video/webm',
        'video/quicktime', // .mov files
    ];
    private const CHUNK_SIZE = 5242880; // 5MB chunks
    private const STORAGE_DISK = 'public';
    private const STORAGE_PATH = 'videos';

    private readonly VideoThumbnailService $thumbnailService;

    public function __construct(VideoThumbnailService $thumbnailService)
    {
        $this->thumbnailService = $thumbnailService;
    }

    /**
     * Initialize a new chunked upload session
     */
    public function initializeUpload(UploadedFile $file, int $eventId): array
    {
        $this->validateFile($file);

        $uploadId = Str::uuid()->toString();
        $filename = $this->generateFilename($file);
        $chunkPath = $this->getChunkPath($uploadId);

        // Create chunk directory
        Storage::disk(self::STORAGE_DISK)->makeDirectory($chunkPath);

        // Store metadata in session/cache for tracking
        $metadata = [
            'upload_id' => $uploadId,
            'event_id' => $eventId,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'total_size' => $file->getSize(),
            'chunks_received' => 0,
            'bytes_received' => 0,
            'status' => 'initialized',
        ];

        cache()->put("upload:{$uploadId}", $metadata, now()->addHours(24));

        return [
            'upload_id' => $uploadId,
            'chunk_size' => self::CHUNK_SIZE,
            'total_chunks' => (int) ceil($file->getSize() / self::CHUNK_SIZE),
        ];
    }

    /**
     * Handle a chunk upload
     */
    public function uploadChunk(
        string $uploadId,
        UploadedFile $chunk,
        int $chunkIndex,
        int $totalChunks
    ): array {
        $metadata = cache()->get("upload:{$uploadId}");

        if (!$metadata) {
            throw new \InvalidArgumentException('Invalid upload ID. Upload session may have expired.');
        }

        // Validate chunk
        if ($chunk->getSize() > self::CHUNK_SIZE) {
            throw new \InvalidArgumentException('Chunk size exceeds maximum allowed size.');
        }

        // Store chunk
        $chunkPath = $this->getChunkPath($uploadId);
        $chunkFilename = "chunk_{$chunkIndex}";
        Storage::disk(self::STORAGE_DISK)->put(
            "{$chunkPath}/{$chunkFilename}",
            file_get_contents($chunk->getRealPath())
        );

        // Update metadata
        $metadata['chunks_received']++;
        $metadata['bytes_received'] += $chunk->getSize();
        $metadata['status'] = $metadata['chunks_received'] >= $totalChunks ? 'complete' : 'uploading';
        $metadata['last_chunk_index'] = $chunkIndex;

        cache()->put("upload:{$uploadId}", $metadata, now()->addHours(24));

        // Calculate progress percentage
        $progress = (int) min(100, ($metadata['chunks_received'] / $totalChunks) * 100);

        return [
            'upload_id' => $uploadId,
            'chunk_index' => $chunkIndex,
            'chunks_received' => $metadata['chunks_received'],
            'total_chunks' => $totalChunks,
            'bytes_received' => $metadata['bytes_received'],
            'total_bytes' => $metadata['total_size'],
            'progress' => $progress,
            'status' => $metadata['status'],
        ];
    }

    /**
     * Finalize upload by combining chunks and creating Video record
     */
    public function finalizeUpload(string $uploadId, array $videoData): Video
    {
        $metadata = cache()->get("upload:{$uploadId}");

        if (!$metadata) {
            throw new \InvalidArgumentException('Invalid upload ID. Upload session may have expired.');
        }

        if ($metadata['status'] !== 'complete') {
            throw new \InvalidArgumentException('Upload is not complete. All chunks must be received first.');
        }

        // Combine chunks into final file
        $finalPath = $this->combineChunks($uploadId, $metadata['original_filename']);

        // Clean up chunks
        $this->cleanupChunks($uploadId);

        // Create video record (without thumbnail initially)
        $video = Video::create([
            'event_id' => $metadata['event_id'],
            'upload_id' => $uploadId,
            'title' => $videoData['title'] ?? pathinfo($metadata['original_filename'], PATHINFO_FILENAME),
            'description' => $videoData['description'] ?? null,
            'video_url' => Storage::disk(self::STORAGE_DISK)->url($finalPath),
            'thumbnail_url' => $videoData['thumbnail_url'] ?? null,
            'duration_seconds' => $videoData['duration_seconds'] ?? 0,
            'is_featured' => $videoData['is_featured'] ?? false,
            'sort_order' => $videoData['sort_order'] ?? 0,
            'file_size' => $metadata['total_size'],
            'mime_type' => $metadata['mime_type'],
            'original_filename' => $metadata['original_filename'],
        ]);

        // Generate thumbnail automatically if not provided
        if (empty($videoData['thumbnail_url'])) {
            try {
                $this->thumbnailService->generateForVideo($video);
            } catch (\Exception $e) {
                // Log error but don't fail the upload
                // Thumbnail generation can be retried later
            }
        }

        // Clear upload metadata from cache
        cache()->forget("upload:{$uploadId}");

        return $video;
    }

    /**
     * Resume an interrupted upload - return already received chunks
     */
    public function resumeUpload(string $uploadId): array
    {
        $metadata = cache()->get("upload:{$uploadId}");

        if (!$metadata) {
            throw new \InvalidArgumentException('Invalid upload ID. Upload session may have expired.');
        }

        $chunkPath = $this->getChunkPath($uploadId);
        $chunks = Storage::disk(self::STORAGE_DISK)->files($chunkPath);

        $receivedChunks = [];
        foreach ($chunks as $chunk) {
            if (preg_match('/chunk_(\d+)/', basename($chunk), $matches)) {
                $receivedChunks[] = (int) $matches[1];
            }
        }

        sort($receivedChunks);

        return [
            'upload_id' => $uploadId,
            'original_filename' => $metadata['original_filename'],
            'total_size' => $metadata['total_size'],
            'bytes_received' => $metadata['bytes_received'],
            'chunks_received' => $metadata['chunks_received'],
            'total_chunks' => (int) ceil($metadata['total_size'] / self::CHUNK_SIZE),
            'received_chunks' => $receivedChunks,
            'missing_chunks' => array_values(array_diff(
                range(0, (int) ceil($metadata['total_size'] / self::CHUNK_SIZE) - 1),
                $receivedChunks
            )),
            'status' => $metadata['status'],
        ];
    }

    /**
     * Cancel an upload and clean up resources
     */
    public function cancelUpload(string $uploadId): void
    {
        $this->cleanupChunks($uploadId);
        cache()->forget("upload:{$uploadId}");
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File size exceeds maximum allowed size of 1GB.');
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new \InvalidArgumentException(
                'Invalid file type. Only MP4, WebM, and MOV files are allowed.'
            );
        }

        // Additional check by file extension for better accuracy
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['mp4', 'webm', 'mov'];
        if (!in_array($extension, $allowedExtensions, true)) {
            throw new \InvalidArgumentException(
                'Invalid file extension. Only .mp4, .webm, and .mov files are allowed.'
            );
        }
    }

    /**
     * Generate unique filename for storage
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return Str::uuid()->toString() . '.' . $extension;
    }

    /**
     * Get chunk storage path for an upload
     */
    private function getChunkPath(string $uploadId): string
    {
        return self::STORAGE_PATH . '/chunks/' . $uploadId;
    }

    /**
     * Combine all chunks into final video file
     */
    private function combineChunks(string $uploadId, string $originalFilename): string
    {
        $chunkPath = $this->getChunkPath($uploadId);
        $chunks = Storage::disk(self::STORAGE_DISK)->files($chunkPath);

        // Sort chunks by index
        usort($chunks, function ($a, $b) {
            preg_match('/chunk_(\d+)/', basename($a), $aMatches);
            preg_match('/chunk_(\d+)/', basename($b), $bMatches);
            return ($aMatches[1] ?? 0) <=> ($bMatches[1] ?? 0);
        });

        // Generate final filename
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $finalFilename = Str::uuid()->toString() . '.' . $extension;
        $finalPath = self::STORAGE_PATH . '/' . $finalFilename;

        // Combine chunks
        $combinedContent = '';
        foreach ($chunks as $chunk) {
            $combinedContent .= Storage::disk(self::STORAGE_DISK)->get($chunk);
        }

        Storage::disk(self::STORAGE_DISK)->put($finalPath, $combinedContent);

        return $finalPath;
    }

    /**
     * Clean up chunk files
     */
    private function cleanupChunks(string $uploadId): void
    {
        $chunkPath = $this->getChunkPath($uploadId);
        Storage::disk(self::STORAGE_DISK)->deleteDirectory($chunkPath);
    }
}

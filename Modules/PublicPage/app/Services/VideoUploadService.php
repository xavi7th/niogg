<?php

namespace Modules\PublicPage\Services;

use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;
use Modules\PublicPage\Jobs\GenerateVideoThumbnail;

class VideoUploadService
{
  private const MAX_FILE_SIZE = 1073741824; // 1GB in bytes

  private const ALLOWED_MIME_TYPES = [
    'video/mp4',
    'video/webm',
    'video/quicktime', // .mov files
  ];

  private const CHUNK_SIZE = 10485760; // 10MB chunks

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
  public function initializeUpload(string $filename, int $fileSize, string $mimeType, int $eventId): array
  {
    $this->validateMetadata($filename, $fileSize, $mimeType);

    $uploadId = Str::uuid()->toString();
    $extension = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $chunkPath = $this->getChunkPath($uploadId);

    // Create chunk directory
    Storage::disk(self::STORAGE_DISK)->makeDirectory($chunkPath);

    // Store metadata in session/cache for tracking
    $metadata = [
      'upload_id' => $uploadId,
      'event_id' => $eventId,
      'original_filename' => $filename,
      'mime_type' => $mimeType,
      'total_size' => $fileSize,
      'chunks_received' => 0,
      'bytes_received' => 0,
      'status' => 'initialized',
    ];

    cache()->put('upload:' . $uploadId, $metadata, now()->addHours(24));

    return [
      'upload_id' => $uploadId,
      'chunk_size' => self::CHUNK_SIZE,
      'total_chunks' => (int) ceil($fileSize / self::CHUNK_SIZE),
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
    $metadata = cache()->get('upload:' . $uploadId);

    if ( ! $metadata) {
      throw new InvalidArgumentException('Invalid upload ID. Upload session may have expired.');
    }

    // Validate chunk index is within expected range
    $expectedChunks = (int) ceil($metadata['total_size'] / self::CHUNK_SIZE);
    if ($chunkIndex < 0 || $chunkIndex >= $expectedChunks) {
      throw new InvalidArgumentException('Invalid chunk index ' . $chunkIndex . '. Expected range: 0 to ' . ($expectedChunks - 1));
    }

    // Validate chunk size
    if ($chunk->getSize() > self::CHUNK_SIZE) {
      throw new InvalidArgumentException('Chunk size exceeds maximum allowed size.');
    }

    // Store chunk
    $chunkPath = $this->getChunkPath($uploadId);
    $chunkFilename = 'chunk_' . $chunkIndex;
    Storage::disk(self::STORAGE_DISK)->put(
        $chunkPath . '/' . $chunkFilename,
        file_get_contents($chunk->getRealPath())
    );

    // Use cache lock for thread-safe counter updates
    $lock = cache()->lock('upload:' . $uploadId . ':lock', 10);

    try {
      $lock->block(5);

      // Get fresh metadata
      $metadata = cache()->get('upload:' . $uploadId);

      // Atomically increment bytes received
      $metadata['bytes_received'] = ($metadata['bytes_received'] ?? 0) + $chunk->getSize();

      // Track which chunk indices we've received (for duplicate detection)
      if ( ! isset($metadata['received_indices'])) {
        $metadata['received_indices'] = [];
      }
      $metadata['received_indices'][] = $chunkIndex;
      $metadata['received_indices'] = array_unique($metadata['received_indices']);
      $chunksReceived = count($metadata['received_indices']);

      $status = $chunksReceived >= $expectedChunks ? 'complete' : 'uploading';

      // Update metadata
      $metadata['chunks_received'] = $chunksReceived;
      $metadata['status'] = $status;
      $metadata['last_chunk_index'] = $chunkIndex;

      cache()->put('upload:' . $uploadId, $metadata, now()->addHours(24));

      // Calculate progress percentage
      $progress = (int) min(100, ($chunksReceived / $expectedChunks) * 100);

      return [
        'upload_id' => $uploadId,
        'chunk_index' => $chunkIndex,
        'chunks_received' => $chunksReceived,
        'total_chunks' => $expectedChunks,
        'bytes_received' => $metadata['bytes_received'],
        'total_bytes' => $metadata['total_size'],
        'progress' => $progress,
        'status' => $status,
      ];
    } finally {
      $lock?->release();
    }
  }

  /**
   * Finalize upload by combining chunks and creating Video record
   */
  public function finalizeUpload(string $uploadId, array $videoData): Video
  {
    $metadata = cache()->get('upload:' . $uploadId);

    if ( ! $metadata) {
      throw new InvalidArgumentException('Invalid upload ID. Upload session may have expired.');
    }

    // Verify all chunks received using tracked indices
    $expectedChunks = (int) ceil($metadata['total_size'] / self::CHUNK_SIZE);
    $receivedIndices = $metadata['received_indices'] ?? [];
    $actualChunks = count($receivedIndices);

    if ($actualChunks < $expectedChunks) {
      // Find missing chunks
      $missingChunks = array_diff(range(0, $expectedChunks - 1), $receivedIndices);
      throw new InvalidArgumentException(
          'Upload is not complete. Expected ' . $expectedChunks . ' chunks, but only ' . $actualChunks . ' received. ' .
          'Missing chunks: ' . implode(', ', $missingChunks)
      );
    }

    // Combine chunks into final file
    $finalPath = $this->combineChunks($uploadId, $metadata['original_filename']);

    // Clean up chunks
    $this->cleanupChunks($uploadId);

    $isMp4 = $metadata['mime_type'] === 'video/mp4';

    // Create video record (without thumbnail initially)
    $video = Video::create([
      'event_id' => $metadata['event_id'],
      'upload_id' => $uploadId,
      'title' => $videoData['title'] ?? pathinfo($metadata['original_filename'], PATHINFO_FILENAME),
      'description' => $videoData['description'] ?? NULL,
      'video_url' => Storage::disk(self::STORAGE_DISK)->url($finalPath),
      'thumbnail_url' => $videoData['thumbnail_url'] ?? NULL,
      'duration_seconds' => $videoData['duration_seconds'] ?? 0,
      'is_featured' => $videoData['is_featured'] ?? FALSE,
      'sort_order' => $videoData['sort_order'] ?? 0,
      'file_size' => $metadata['total_size'],
      'mime_type' => $metadata['mime_type'],
      'original_filename' => $metadata['original_filename'],
      'conversion_status' => $isMp4 ? 'completed' : 'pending',
    ]);

    // Dispatch thumbnail generation asynchronously if not provided
    if (empty($videoData['thumbnail_url'])) {
        GenerateVideoThumbnail::dispatch($video);
    }

    // Dispatch conversion job for non-MP4 videos
    if ( ! $isMp4) {
      Bus::dispatch(new ConvertVideoToMp4($video));
    }

    // Clear upload metadata from cache
    cache()->forget('upload:' . $uploadId);

    return $video;
  }

  /**
   * Resume an interrupted upload - return already received chunks
   */
  public function resumeUpload(string $uploadId): array
  {
    $metadata = cache()->get('upload:' . $uploadId);

    if ( ! $metadata) {
      throw new InvalidArgumentException('Invalid upload ID. Upload session may have expired.');
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
    cache()->forget('upload:' . $uploadId);
  }

  /**
   * Validate uploaded file
   */
  private function validateFile(UploadedFile $file): void
  {
    // Check file size
    if ($file->getSize() > self::MAX_FILE_SIZE) {
      throw new InvalidArgumentException('File size exceeds maximum allowed size of 1GB.');
    }

    // Check MIME type
    $mimeType = $file->getMimeType();
    if ( ! in_array($mimeType, self::ALLOWED_MIME_TYPES, TRUE)) {
      throw new InvalidArgumentException(
          'Invalid file type. Only MP4, WebM, and MOV files are allowed.'
      );
    }

    // Additional check by file extension for better accuracy
    $extension = mb_strtolower($file->getClientOriginalExtension());
    $allowedExtensions = ['mp4', 'webm', 'mov'];
    if ( ! in_array($extension, $allowedExtensions, TRUE)) {
      throw new InvalidArgumentException(
          'Invalid file extension. Only .mp4, .webm, and .mov files are allowed.'
      );
    }
  }

  /**
   * Validate upload metadata (for initialize without file upload)
   */
  private function validateMetadata(string $filename, int $fileSize, string $mimeType): void
  {
    // Check file size
    if ($fileSize > self::MAX_FILE_SIZE) {
      throw new InvalidArgumentException('File size exceeds maximum allowed size of 1GB.');
    }

    // Check MIME type
    if ( ! in_array($mimeType, self::ALLOWED_MIME_TYPES, TRUE)) {
      throw new InvalidArgumentException(
          'Invalid file type. Only MP4, WebM, and MOV files are allowed.'
      );
    }

    // Check by file extension for better accuracy
    $extension = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowedExtensions = ['mp4', 'webm', 'mov'];
    if ( ! in_array($extension, $allowedExtensions, TRUE)) {
      throw new InvalidArgumentException(
          'Invalid file extension. Only .mp4, .webm, and .mov files are allowed.'
      );
    }
  }

  /**
   * Generate unique filename for storage
   */
  private function generateFilename(UploadedFile $file): string
  {
    $extension = mb_strtolower($file->getClientOriginalExtension());

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
   * Combine all chunks into final video file using streaming
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
    $extension = mb_strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
    $finalFilename = Str::uuid()->toString() . '.' . $extension;
    $finalPath = self::STORAGE_PATH . '/' . $finalFilename;

    // Stream chunks directly to final file (no memory buildup)
    $disk = Storage::disk(self::STORAGE_DISK);
    $tempPath = sys_get_temp_dir() . '/' . $finalFilename;

    $outStream = fopen($tempPath, 'wb');
    if ($outStream === FALSE) {
      throw new Exception('Failed to create temporary file for chunk combination.');
    }

    try {
      foreach ($chunks as $chunk) {
        $chunkContent = $disk->get($chunk);
        if ($chunkContent !== FALSE && $chunkContent !== '') {
          fwrite($outStream, $chunkContent);
        }
      }
    } finally {
      fclose($outStream);
    }

    // Store the combined file
    $disk->put($finalPath, file_get_contents($tempPath));
    unlink($tempPath);

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

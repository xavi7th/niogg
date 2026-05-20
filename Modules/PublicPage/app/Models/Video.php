<?php

namespace Modules\PublicPage\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\ResponseCache\Facades\ResponseCache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PublicPage\Database\Factories\VideoFactory;

class Video extends Model
{
  use HasFactory;

  protected static function newFactory(): VideoFactory
  {
    return VideoFactory::new();
  }

  protected $fillable = [
    'event_id',
    'title',
    'description',
    'video_url',
    'thumbnail_url',
    'custom_thumbnail_url',
    'duration_seconds',
    'is_featured',
    'sort_order',
    'upload_id',
    'file_size',
    'mime_type',
    'original_filename',
    'conversion_status',
    'conversion_started_at',
    'conversion_completed_at',
    'conversion_error',
  ];

  /**
   * The accessors to append to the model's array form.
   */
  protected $appends = [
    'thumbnail_url',
  ];

  protected $casts = [
    'is_featured' => 'boolean',
    'duration_seconds' => 'integer',
    'sort_order' => 'integer',
    'file_size' => 'integer',
    'conversion_started_at' => 'datetime',
    'conversion_completed_at' => 'datetime',
  ];

  protected static function booted(): void
  {
    static::saved(fn () => ResponseCache::clear());
    static::deleted(fn () => ResponseCache::clear());
  }

  /**
   * Event relationship with cascade delete
   */
  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class);
  }

  /**
   * Get featured videos
   */
  public function scopeFeatured($query)
  {
    return $query->where('is_featured', TRUE);
  }

  /**
   * Order by sort_order then created_at
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('sort_order')->orderBy('created_at');
  }

  /**
   * Format duration from seconds to MM:SS (Accessor)
   */
  public function getFormatDurationAttribute(): string
  {
    $minutes = (int) ($this->duration_seconds / 60);
    $seconds = $this->duration_seconds % 60;

    return sprintf('%d:%02d', $minutes, $seconds);
  }

  /**
   * Get thumbnail URL with fallback chain and cache busting (Accessor)
   *
   * Priority: Custom -> Auto-generated -> Placeholder
   * Applies cache-busting (?v={timestamp}) to dynamic thumbnails
   */
  public function getThumbnailUrlAttribute(): string
  {
    // Priority 1: Custom thumbnail
    if ( ! empty($this->attributes['custom_thumbnail_url'] ?? NULL)) {
      $url = $this->attributes['custom_thumbnail_url'];

      return strtok($url, '?') . '?v=' . $this->updated_at->timestamp;
    }

    // Priority 2: Auto-generated thumbnail
    if ( ! empty($this->attributes['thumbnail_url'] ?? NULL)) {
      $url = $this->attributes['thumbnail_url'];

      return strtok($url, '?') . '?v=' . $this->updated_at->timestamp;
    }

    // Priority 3: Placeholder (no cache busting needed)
    return '/images/video-placeholder-default.jpg';
  }
}

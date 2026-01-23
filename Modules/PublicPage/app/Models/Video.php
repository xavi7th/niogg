<?php

namespace Modules\PublicPage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
      'event_id',
      'title',
      'description',
      'video_url',
      'thumbnail_url',
      'duration_seconds',
      'is_featured',
      'sort_order',
    ];

    protected $casts = [
      'is_featured' => 'boolean',
      'duration_seconds' => 'integer',
      'sort_order' => 'integer',
    ];

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
}

<?php

namespace Modules\PublicPage\Models;

use App\Models\BaseModel;
use Spatie\ResponseCache\Facades\ResponseCache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PublicPage\Database\Factories\EventPhotoFactory;

class EventPhoto extends BaseModel
{
    use HasFactory;

    protected $table = 'event_photos';

    protected $fillable = [
      'event_id',
      'photo_url',
      'thumbnail_url',
      'thumbnail_generation',
      'alt_text',
      'sort_order',
    ];

    protected $casts = [
      'sort_order' => 'integer',
    ];

    protected static function newFactory(): EventPhotoFactory
    {
        return EventPhotoFactory::new();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected static function booted(): void
    {
        static::saved(fn () => ResponseCache::clear());
        static::deleted(fn () => ResponseCache::clear());
    }

    public function scopeOrdered($query): void
    {
        $query->orderBy('sort_order')->orderBy('created_at');
    }
}

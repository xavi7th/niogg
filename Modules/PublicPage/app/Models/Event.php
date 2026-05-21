<?php

namespace Modules\PublicPage\Models;

use App\Models\BaseModel;
use Illuminate\Support\Str;
use Spatie\ResponseCache\Facades\ResponseCache;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PublicPage\Database\Factories\EventFactory;

class Event extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): EventFactory
    {
        return EventFactory::new();
    }

    protected $fillable = [
      'name',
      'description',
      'icon',
      'category',
      'event_date',
      'slug',
      'is_published',
    ];

    protected $casts = [
      'event_date' => 'date',
      'is_published' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            if ( ! $model->slug) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });

        static::saved(fn () => ResponseCache::clear());
        static::deleted(fn () => ResponseCache::clear());
    }

    /**
     * Generate unique slug from name
     */
    protected static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = static::whereRaw('slug = ? OR slug LIKE ?', [$slug, $slug . '-%'])->count();

        return $count ? $slug . '-' . ($count + 1) : $slug;
    }

    /**
     * Videos relationship
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class)->ordered();
    }

    /**
     * Get published events
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', TRUE);
    }

    /**
     * Order events by date
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $direction  Sort direction: 'asc' (oldest first) or 'desc' (newest first)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query, ?string $direction = 'desc')
    {
        return $query->orderBy('event_date', $direction);
    }
}

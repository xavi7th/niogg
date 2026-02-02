# Laravel + Inertia.js Sorting Patterns

**Research Date:** 2026-02-02

## Key Findings

### 1. URL-Based Sorting (Best Practice)

Store sort state in URL query params for bookmarkable URLs.

**Backend:**
```php
$sort = $request->input('sort', 'newest'); // newest|oldest
$direction = $sort === 'newest' ? 'desc' : 'asc';
$events = Event::orderBy('event_date', $direction)->get();
```

### 2. Dynamic Scope Parameters

**Update Event model:**
```php
public function scopeOrdered($query, $direction = 'desc')
{
    $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';
    return $query->orderBy('event_date', $direction);
}
```

### 3. Inertia.js URL Preservation

```svelte
import { router } from '@inertiajs/svelte';

function handleSort(sort) {
  router.get(route('events.media-showcase'),
    { sort },
    { replace: true }  // Don't add to history
  );
}
```

### 4. Frontend Reactive State

```svelte
let sort = $page.url.searchParams.get('sort') || 'newest';
```

---

## Action Items for This Project

1. Update `Event::scopeOrdered()` to accept `$direction` parameter
2. Add sort toggle to `EventsMediaShowcase.svelte`
3. Pass sort param from controller via `request()->input('sort')`
4. Use `router.get()` with `replace: true` for sort changes

---

*Sources: Inertia.js docs, Laracasts, dev.to*

# Phase 04: Event Sorting - Research

**Researched:** 2026-02-04
**Domain:** Laravel 10 + Inertia.js v0 + Svelte - URL-based sorting with query parameters
**Confidence:** HIGH

## Summary

This phase implements event sorting (newest-first / oldest-first) using URL query parameters. The implementation requires:
1. **Backend:** Modify controller to read `?sort=` query parameter and apply appropriate ordering
2. **Frontend:** Create sort toggle controls on EventTimeline and EventsGrid components
3. **Navigation:** Use Inertia Link with query params to maintain sort state across navigation

**Primary recommendation:** Use Laravel's `request()->input('sort')` in controller, Svelte's `$page.url.searchParams` to read current state, and `<Link>` component with query parameters to toggle sort order. No additional packages needed - standard Laravel and Inertia.js capabilities are sufficient.

## Standard Stack

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| **Laravel Query Builder** | 10.x | `orderBy()`, `reorder()` methods | Eloquent's standard sorting API |
| **Inertia.js Svelte Adapter** | v0 | `$page` store, `<Link>` component | Official Inertia integration for Svelte |
| **Svelte stores** | Built-in | `$page` reactive store | Standard Svelte reactivity for Inertia page data |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| **Laravel Request** | Built-in | `$request->input('sort')` | Reading query parameters |
| **URLSearchParams** | Browser API | `$page.url.searchParams` | Parsing URL query strings in Svelte |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Native Inertia Link | `router.get()` manual visit | Link is declarative and cleaner; `router.get()` useful for dynamic params |
| URL query params | localStorage/shared store | Query params enable shareable links, localStorage is per-browser |

**Installation:** No additional packages needed - all functionality exists in current stack.

## Architecture Patterns

### Recommended Project Structure
```
Modules/PublicPage/
├── app/Http/Controllers/
│   └── EventsMediaShowcaseController.php  # Add sort parameter handling
├── resources/js/Pages/
│   ├── Components/
│   │   ├── EventTimeline.svelte           # Add sort toggle control
│   │   └── EventsGrid.svelte              # Add sort toggle control
│   └── EventsMediaShowcase.svelte         # Optional: sort state management
└── app/Models/
    └── Event.php                           # Modify ordered() scope to accept direction
```

### Pattern 1: URL Parameter Sorting
**What:** Server-side sorting controlled via URL query parameters (`?sort=newest|oldest`)

**When to use:** When sort state should be shareable via URL and persist across navigation

**Example - Controller (Laravel 10):**
```php
// Source: Laravel 10 Query Builder docs + existing Event model pattern
// https://laravel.com/docs/10.x/queries#ordering-grouping-limit-and-offset

public function index(Request $request)
{
    $sort = $request->input('sort', 'newest'); // Default: newest
    $direction = $sort === 'newest' ? 'desc' : 'asc';

    $events = Event::with('videos')
        ->published()
        ->orderBy('event_date', $direction)
        ->get();

    return Inertia::render('PublicPage::EventsMediaShowcase', [
        'events' => $events,
        'sort' => $sort, // Pass back to frontend
    ]);
}
```

### Pattern 2: Inertia Link with Query Parameters
**What:** Declarative navigation with sort state preserved in URL

**When to use:** Creating sort toggle controls that update URL and fetch sorted data

**Example - Svelte Component:**
```svelte
<!-- Source: Inertia.js Links documentation -->
<!-- https://inertiajs.com/docs/v2/the-basics/links -->

<script>
  import { Link } from '@inertiajs/svelte';
  import { page } from '@inertiajs/svelte';

  export let currentSort = 'newest';

  // Read current sort from URL
  $: urlSort = $page.url.searchParams.get('sort') || 'newest';
  $: currentSort = urlSort;

  const toggleSort = () => {
    const newSort = currentSort === 'newest' ? 'oldest' : 'newest';
    // Link component automatically handles URL construction
  };
</script>

<div class="sort-control">
  <span>Sort by:</span>
  <Link href="?sort=newest" class:active={currentSort === 'newest'}>
    Newest First
  </Link>
  <Link href="?sort=oldest" class:active={currentSort === 'oldest'}>
    Oldest First
  </Link>
</div>

<style>
  .active {
    color: #ff7607;
    font-weight: 600;
  }
</style>
```

### Pattern 3: Model Scope with Direction Parameter
**What:** Flexible Eloquent scope for dynamic sorting

**When to use:** Centralizing sort logic in model for reusability

**Example - Event Model:**
```php
// Source: Laravel 10 Eloquent scopes
// https://laravel.com/docs/10.x/eloquent#local-scopes

public function scopeOrdered($query, ?string $direction = 'desc')
{
    return $query->orderBy('event_date', $direction);
}

// Usage in controller:
$direction = $request->input('sort') === 'oldest' ? 'asc' : 'desc';
$events = Event::ordered($direction)->get();
```

### Anti-Patterns to Avoid
- **Client-side sorting only:** Sorts displayed data but breaks when new data is fetched or page is refreshed
- **localStorage persistence:** Prevents shareable URLs and conflicts with server-side rendering
- **Global header control:** Violates phase context decision - controls must be page-level
- **Hardcoded sort in scope:** The current `ordered()` scope hardcodes `desc` - needs parameterization

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| URL parsing for query params | Manual string splitting | `$page.url.searchParams.get('sort')` | Built-in browser API, handles edge cases |
| Sort state management | Custom Svelte store or writable | `$page` reactive store from Inertia | Automatically synced with URL |
| History management | `window.history.pushState()` | `<Link>` or `router.get()` | Inertia manages history automatically |

**Key insight:** Inertia.js provides all necessary state management through the `$page` store. Building custom sort state creates synchronization bugs and violates Inertia's design philosophy.

## Common Pitfalls

### Pitfall 1: Sort State Not Persisting Across Navigation
**What goes wrong:** User selects sort order, navigates away, returns to find sort reset to default

**Why it happens:** Sort state stored in component-local variable instead of URL

**How to avoid:** Always use URL query parameters (`?sort=oldest`) for sort state

**Warning signs:** Sort resets when clicking browser back button or refreshing page

### Pitfall 2: Query Parameters Not Visible in URL
**What goes wrong:** Backend receives sort parameter but browser URL doesn't update

**Why it happens:** Using `router.reload()` or `Inertia.get()` incorrectly (known Inertia issue #912)

**How to avoid:** Use `<Link>` component for declarative navigation or ensure `preserveState: false` in manual visits

**Warning signs:** Links don't show sort preference when shared

### Pitfall 3: Breaking Shareable Links
**What goes wrong:** User shares link but recipient sees different sort order

**Why it happens:** Sort preference stored in localStorage or component state instead of URL

**How to avoid:** Never use localStorage for user-facing sort preferences

**Warning signs:** URL doesn't change when toggling sort

### Pitfall 4: Forgetting Default Sort
**What goes wrong:** Error or unsorted results when accessing page without `?sort=` parameter

**Why it happens:** Controller doesn't handle missing query parameter

**How to avoid:** Always provide default: `$request->input('sort', 'newest')`

**Warning signs:** Error on initial page load or empty URL

## Code Examples

Verified patterns from official sources:

### Reading Query Parameter in Svelte
```svelte
// Source: Inertia.js Svelte adapter docs
// https://inertiajs.com/docs/v2/the-basics/pages

import { page } from '@inertiajs/svelte';

// Access current sort from URL
$: currentSort = $page.url.searchParams.get('sort') || 'newest';
```

### Sort Toggle Component
```svelte
<script>
  import { Link } from '@inertiajs/svelte';
  import { page } from '@inertiajs/svelte';

  $: currentSort = $page.url.searchParams.get('sort') || 'newest';
</script>

<nav class="sort-toggle" aria-label="Sort events">
  <Link href="?sort=newest" class:active={currentSort === 'newest'}>
    Newest
  </Link>
  <span>/</span>
  <Link href="?sort=oldest" class:active={currentSort === 'oldest'}>
    Oldest
  </Link>
</nav>

<style>
  .sort-toggle {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }

  .active {
    color: #ff7607;
    font-weight: 600;
    text-decoration: underline;
  }
</style>
```

### Controller with Sort Handling
```php
// Source: Laravel 10 Query Builder documentation
// https://laravel.com/docs/10.x/queries#ordering

use Illuminate\Http\Request;

public function index(Request $request)
{
    $sort = $request->input('sort', 'newest');
    $direction = $sort === 'newest' ? 'desc' : 'asc';

    $events = Event::with('videos')
        ->published()
        ->orderBy('event_date', $direction)
        ->get();

    return Inertia::render('PublicPage::EventsMediaShowcase', [
        'events' => $events,
    ]);
}
```

### Active Sort Link Styling
```svelte
<!-- Create reusable sort link component -->
<script>
  import { Link } from '@inertiajs/svelte';
  import { page } from '@inertiajs/svelte';

  export let label;
  export let value;

  $: isActive = $page.url.searchParams.get('sort') === value;
</script>

<Link href="?sort={value}" class:active={isActive} aria-current={isActive ? 'true' : undefined}>
  {label}
</Link>

<style>
  :global(a.active) {
    color: #ff7607;
    font-weight: 600;
  }
</style>
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Component-local sort state | URL query parameters | Inertia.js v0+ | Sort is now shareable and persists |
| Manual history management | Inertia Link/router | Since Inertia.js launch | Automatic history sync |

**Deprecated/outdated:**
- **SvelteKit-specific query param packages** (svelte-query-params, sveltekit-search-params): Not needed with Inertia.js - use `$page` store instead
- **localStorage for preferences**: Violates shareable link requirement

## Open Questions

None - all implementation aspects are clear with standard Laravel + Inertia.js + Svelte stack.

## Sources

### Primary (HIGH confidence)
- **[Laravel 10.x Query Builder - Ordering](https://laravel.com/docs/10.x/queries#ordering-grouping-limit-and-offset)** - Official Laravel documentation on `orderBy()`, `latest()`, `oldest()` methods
- **[Inertia.js - Links Documentation](https://inertiajs.com/docs/v2/the-basics/links)** - Official Inertia.js documentation on Link component and `page.url`
- **[Inertia.js - Manual Visits Documentation](https://inertiajs.com/docs/v2/the-basics/manual-visits)** - Official documentation on `router.get()` and visit options
- **[Inertia.js - Pages Documentation](https://inertiajs.com/docs/v2/the-basics/pages)** - Official documentation on the page object structure

### Secondary (MEDIUM confidence)
- **[Stack Overflow - Server-side multi-column sorting in Laravel Inertia](https://stackoverflow.com/questions/67153139/initialize-server-side-multi-column-sorting-in-laravel-inertia-vue)** - Community discussion on Inertia sorting patterns
- **[Laracasts - Get Inertia query parameters](https://laracasts.com/discuss/channels/inertia/get-inertia-query-parameters)** - Community discussion on accessing query params
- **[Create Laravel CRUD Column Sorting with Inertia and Vue](https://blog.devgenius.io/laravel-crud-column-sorting-with-inertia-and-vue-6bb16c30d3e0)** - Tutorial on Inertia sorting implementation (Vue, but patterns apply to Svelte)

### Tertiary (LOW confidence)
- **[SvelteKit query parameters and page.js - Reddit](https://www.reddit.com/r/sveltejs/comments/y7ssim/sveltekit_query_parameters_and_pagejs/)** - General Svelte query param discussion (not Inertia-specific)
- **[Access URL query string in Svelte - Stack Overflow](https://stackoverflow.com/questions/66637632/access-url-query-string-in-svelte)** - General Svelte URL parsing (SvelteKit-specific, not Inertia)

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - All components are part of current tech stack, well-documented
- Architecture: HIGH - Standard Inertia.js patterns for URL-based state management
- Pitfalls: HIGH - Based on well-documented Inertia.js behavior and URL state management best practices

**Research date:** 2026-02-04
**Valid until:** 2026-03-06 (30 days - stable Laravel/Inertia ecosystem)

---

*Phase: 04-event-sorting*
*Research completed: 2026-02-04*

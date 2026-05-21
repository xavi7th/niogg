# Plan 01-03: Add LazyThumbnail to EventVideosGrid and EventsGrid

**Wave:** 2
**Depends on:** 01-02 (requires placeholder image and enhanced LazyThumbnail)
**Files modified:**

- `/Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte`
- `/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte`
  **Autonomous:** Yes

## Tasks

<task type="auto">
  <name>Task 1: Update EventVideosGrid.svelte with LazyThumbnail</name>
  <files>/Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte</files>
  <action>
    1. Add import: `import LazyThumbnail from '@publicpage-pages/Components/LazyThumbnail.svelte';`
    2. Line 56: Replace `<img src={video.thumbnail_url} alt={video.title} loading="lazy" />`
       with `<LazyThumbnail src={video.thumbnail_url} alt={video.title} placeholder="/images/video-placeholder-default.jpg" class="card-thumbnail" />`
  </action>
  <verify>grep -n "LazyThumbnail\|placeholder" /Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte</verify>
  <done>EventVideosGrid.svelte imports and uses LazyThumbnail component with placeholder</done>
</task>

<task type="auto">
  <name>Task 2: Update EventsGrid.svelte with LazyThumbnail</name>
  <files>/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte</files>
  <action>
    1. Add import: `import LazyThumbnail from './LazyThumbnail.svelte';` (same directory)
    2. Line 93: Replace `<img src={video.thumbnail_url} alt={video.title} loading="lazy" />`
       with `<LazyThumbnail src={video.thumbnail_url} alt={video.title} placeholder="/images/video-placeholder-default.jpg" class="card-thumbnail" />`
  </action>
  <verify>grep -n "LazyThumbnail\|placeholder" /Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte</verify>
  <done>EventsGrid.svelte imports and uses LazyThumbnail component with placeholder</done>
</task>

<task type="auto">
  <name>Task 3: Run Prettier on modified files</name>
  <files>/Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte, /Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte</files>
  <action>Run `npm run format` to ensure consistent formatting</action>
  <verify>npm run format exits without errors</verify>
  <done>Modified files formatted consistently with Prettier</done>
</task>

<task type="checkpoint:human-verify" gate="blocking">
  <what-built>LazyThumbnail integrated into EventVideosGrid and EventsGrid</what-built>
  <how-to-verify>
    1. Test EventVideosGrid view: thumbnails display correctly
    2. Test EventsGrid view: thumbnails display correctly
    3. Test SupportingVideoGrid view: confirm already using LazyThumbnail (no changes needed)
    4. Test lazy loading: scroll down each view and verify images load as you scroll
    5. Test error handling: use DevTools to break a thumbnail URL, verify placeholder appears
    6. Test mobile responsiveness: check views at < 768px viewport width
    7. Check browser console for errors
  </how-to-verify>
  <resume-signal>Type "approved" or describe issues</resume-signal>
</task>

## Verification Criteria

- [ ] EventVideosGrid.svelte uses LazyThumbnail component
- [ ] EventsGrid.svelte uses LazyThumbnail component
- [ ] SupportingVideoGrid.svelte confirmed using LazyThumbnail (already correct)
- [ ] All three grid views display thumbnail images
- [ ] Lazy loading works (images load as you scroll)
- [ ] Placeholder appears for broken/missing thumbnails
- [ ] Mobile responsive (test at < 768px viewport width)
- [ ] No console errors
- [ ] Prettier formatting applied without issues

## must_haves

1. All three video grid views (EventVideosGrid, EventsGrid, SupportingVideoGrid) display thumbnails correctly
2. Lazy loading active on all thumbnail images
3. Error handling shows placeholder for broken thumbnails
4. Mobile responsive thumbnail display
5. No JavaScript console errors

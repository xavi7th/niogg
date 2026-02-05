# Plan 01-02: Create Placeholder Image and Enhance LazyThumbnail Error Handling

**Wave:** 1
**Depends on:** Nothing
**Files modified:**

- `/public/images/video-placeholder-default.jpg` (NEW)
- `/Modules/PublicPage/resources/js/Pages/Components/LazyThumbnail.svelte`
  **Autonomous:** Yes

## Tasks

<task type="auto">
  <name>Task 1: Create public/images directory and placeholder image</name>
  <files>/public/images/video-placeholder-default.jpg</files>
  <action>
    1. Create `/public/images/` directory if it doesn't exist
    2. Create `video-placeholder-default.jpg` - a simple 16:9 gray background with centered video icon using ImageMagick:
       `convert -size 1280x720 xc:#333333 -gravity center -draw "circle 640,360 640,300" /public/images/video-placeholder-default.jpg`
  </action>
  <verify>ls -lh /public/images/video-placeholder-default.jpg</verify>
  <done>Placeholder image file exists at `/public/images/video-placeholder-default.jpg` (16:9 aspect ratio)</done>
</task>

<task type="auto">
  <name>Task 2: Add error handling to LazyThumbnail.svelte</name>
  <files>/Modules/PublicPage/resources/js/Pages/Components/LazyThumbnail.svelte</files>
  <action>
    1. Add `imgError` state variable (boolean, default false)
    2. Add `handleImageError` function that sets `imgError = true` and logs error to console
    3. Wrap `<img>` in conditional: show placeholder prop if `imgError` is true
    4. Add `on:error={handleImageError}` to img tag
  </action>
  <verify>grep -n "on:error\|imgError\|handleImageError" /Modules/PublicPage/resources/js/Pages/Components/LazyThumbnail.svelte</verify>
  <done>LazyThumbnail component has error handler that shows placeholder on load failure</done>
</task>

<task type="auto">
  <name>Task 3: Run Prettier on modified files</name>
  <files>/Modules/PublicPage/resources/js/Pages/Components/LazyThumbnail.svelte</files>
  <action>Run `npm run format` to ensure consistent formatting</action>
  <verify>npm run format exits without errors</verify>
  <done>Modified files formatted consistently with Prettier</done>
</task>

<task type="checkpoint:human-verify" gate="blocking">
  <what-built>Placeholder image and LazyThumbnail error handling</what-built>
  <how-to-verify>
    1. Confirm `/public/images/video-placeholder-default.jpg` is accessible via URL
    2. Open browser DevTools and temporarily modify a thumbnail URL to a non-existent path
    3. Verify placeholder image appears instead of broken-image icon
    4. Check browser console for thumbnail load error logs
    5. Verify valid thumbnails still display correctly
  </how-to-verify>
  <resume-signal>Type "approved" or describe issues</resume-signal>
</task>

## Verification Criteria

- [ ] `/public/images/video-placeholder-default.jpg` file exists and is accessible via URL
- [ ] Placeholder image is 16:9 aspect ratio (1280x720 recommended)
- [ ] LazyThumbnail component has `on:error` handler
- [ ] Broken thumbnail URLs show placeholder image
- [ ] Thumbnail load errors are logged to console
- [ ] Valid thumbnails still display correctly
- [ ] Prettier formatting applied without issues

## must_haves

1. Broken or missing thumbnails show placeholder image instead of broken-image icon
2. LazyThumbnail handles load failures gracefully
3. Error logging for debugging thumbnail issues
4. No broken image icons visible to users

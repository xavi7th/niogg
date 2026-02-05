# Fix enhanced:img Query Parameters in SummarizedWhatWeDo.svelte

## Problem
The `enhanced:img` tags in `SummarizedWhatWeDo.svelte` use malformed query parameters with semicolons (`h=250;300&w=300;450`) causing the `@sveltejs/enhanced-img` preprocessor to fail parsing.

## Files to Modify
- `Modules/PublicPage/resources/js/Pages/Partials/HomePage/SummarizedWhatWeDo.svelte`

## Changes
Fix 4 `enhanced:img` tags (lines 39, 58, 77, 96):

**From:** `?h=250;300&w=300;450&blur=2&fit=cover`
**To:** `?enhanced&w=300`

Using `?enhanced` enables automatic srcset generation, and `w=300` sets the base width.

## Verification
1. Run `npm run dev` - Vite dev server should start without errors
2. Check browser at development URL - images should load correctly
3. Run `vendor/bin/sail bin pint --dirty` to ensure code style

# Plan 01-01: Fix Accessor Naming

**Wave:** 1
**Depends on:** Nothing
**Files modified:**

- `/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte`
- `/Modules/PublicPage/resources/js/Pages/Components/SupportingVideoGrid.svelte`
  **Autonomous:** Yes

## Tasks

<task type="auto">
  <name>Task 1: Fix formatDuration accessor in EventsGrid.svelte</name>
  <files>/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte</files>
  <action>Line 105: Replace `video.formatDuration` with `video.format_duration` to match PHP accessor serialization format (snake_case)</action>
  <verify>grep -n "format_duration" /Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte</verify>
  <done>EventsGrid.svelte uses `format_duration` (not `formatDuration`)</done>
</task>

<task type="auto">
  <name>Task 2: Fix formatDuration accessor in SupportingVideoGrid.svelte</name>
  <files>/Modules/PublicPage/resources/js/Pages/Components/SupportingVideoGrid.svelte</files>
  <action>Lines 41-43: Replace `video.formatDuration` with `video.format_duration` to match PHP accessor serialization format (snake_case)</action>
  <verify>grep -n "format_duration" /Modules/PublicPage/resources/js/Pages/Components/SupportingVideoGrid.svelte</verify>
  <done>SupportingVideoGrid.svelte uses `format_duration` (not `formatDuration`)</done>
</task>

<task type="auto">
  <name>Task 3: Run Prettier on modified files</name>
  <files>/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte, /Modules/PublicPage/resources/js/Pages/Components/SupportingVideoGrid.svelte</files>
  <action>Run `npm run format` to ensure consistent formatting after changes</action>
  <verify>npm run format exits without errors</verify>
  <done>Modified files formatted consistently with Prettier</done>
</task>

<task type="checkpoint:human-verify" gate="blocking">
  <what-built>Fixed accessor naming in EventsGrid.svelte and SupportingVideoGrid.svelte</what-built>
  <how-to-verify>
    1. Open EventsGrid view in browser
    2. Confirm video duration shows as "M:SS" format (not "0:00" or empty)
    3. Open SupportingVideoGrid view in browser
    4. Confirm video duration shows as "M:SS" format
    5. Check browser console for undefined errors related to formatDuration
  </how-to-verify>
  <resume-signal>Type "approved" or describe issues</resume-signal>
</task>

## Verification Criteria

- [ ] EventsGrid.svelte uses `format_duration` (not `formatDuration`)
- [ ] SupportingVideoGrid.svelte uses `format_duration` (not `formatDuration`)
- [ ] Video durations display as "M:SS" format in EventsGrid view
- [ ] Video durations display as "M:SS" format in SupportingVideoGrid view
- [ ] No console errors related to undefined `formatDuration`
- [ ] Prettier formatting applied without issues

## must_haves

1. Accessor naming consistent across all three video grid components
2. Video duration displays correctly (not "0:00" or empty)
3. No JavaScript errors in browser console

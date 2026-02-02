# Technical Concerns

**Analysis Date:** 2026-02-02

## Known Issues

**Mobile Video Playback:**
- Location: `/Modules/PublicPage/resources/js/Pages/MediaShowcase.svelte`
- Issue: Videos not playing on mobile screens
- Status: Recently fixed (commit: 621f5c3)
- Related: Mobile responsive design issues

## Technical Debt

**Module System Complexity:**
- Custom Vite module loader (`/vite-module-loader.js`) adds complexity
- Module-specific aliases require manual maintenance
- Asset concatenation for legacy jQuery/plugins creates maintenance burden
- File: `/vite.config.js`, `/Modules/*/vite.config.js`

**Cache Management:**
- Cache invalidation manually implemented across controllers
- No centralized cache invalidation strategy
- Potential for stale cache data
- Example: `_clearEventsCache()` in `AdminEventController.php:273`

**Video Upload System:**
- Chunked upload implementation is complex (10MB chunks)
- Thread-safe upload handling recently added
- Potential for race conditions in concurrent uploads
- Files: `/Modules/PublicPage/app/Services/VideoUploadService.php`

## Security Considerations

**Admin Authorization:**
- Middleware-based admin checks (`auth`, `admin`)
- Role-based access via `is_admin` and `is_super_admin` flags
- Ensure admin routes are properly protected
- Location: `/Modules/PublicPage/routes/web.php:27`

**File Upload Validation:**
- Video uploads have size limits (1GB max) and mime type validation
- Chunked uploads require proper session tracking
- Ensure upload cleanup for failed/incomplete uploads
- File: `/Modules/PublicPage/app/Services/VideoUploadService.php:16-22`

**CSRF Protection:**
- Laravel CSRF protection enabled
- Ensure all forms include CSRF tokens
- Inertia.js automatically handles CSRF for AJAX requests

## Performance Issues

**N+1 Query Problems:**
- Eager loading not consistently used
- Video relationships may cause N+1 queries on event pages
- Example: `Event::with('videos')` should be used
- Location: `/Modules/PublicPage/app/Http/Controllers/EventsMediaShowcaseController.php`

**Cache Fragmentation:**
- Multiple cache keys for similar data
- No cache tagging strategy for bulk invalidation
- Manual cache clearing in controllers

**Asset Bundle Size:**
- Legacy jQuery/plugins concatenated into single bundle
- SweetAlert2 included even when not needed
- Tailwind CSS may generate unused styles

## Fragile Areas

**Module Dependencies:**
- Modules may have implicit dependencies
- `modules_statuses.json` manually controls activation
- No dependency resolution between modules

**Database Migrations:**
- Migrations span both core `/database/migrations` and module-specific
- Order of migrations can be fragile
- Module migrations not automatically tracked

**Configuration:**
- Environment variables scattered across `.env.example` and actual `.env`
- Some hardcoded values in controllers
- App settings mixed with configuration

## Scalability Concerns

**File Storage:**
- Local storage by default (`public` disk)
- AWS S3 optional but not configured
- Video files can be large (up to 1GB)
- No CDN integration

**Queue System:**
- Video conversion jobs (`ConvertVideoToMp4`) may be slow
- No visible queue worker configuration
- Video processing may block requests

**Database:**
- Single database instance (MariaDB)
- No read replica configuration
- No visible database optimization strategy

## Testing Gaps

**E2E Test Coverage:**
- Playwright tests present but may be incomplete
- Mobile responsive testing needs coverage
- Video upload/playback testing is complex

**Module Isolation:**
- Each module has isolated test suites
- Integration tests across modules may be missing
- Main phpunit.xml only covers `/app` directory

## Maintenance Burdens

**Custom Build System:**
- Non-standard Vite configuration
- Module-specific build logic requires maintenance
- Asset concatenation plugin adds complexity

**Legacy Code:**
- jQuery plugins still used (via concatFiles in Vite)
- Mixed frontend paradigms (Svelte + jQuery)
- Incremental migration path unclear

## Observability Gaps

**Logging:**
- Limited structured logging
- Error tracking via Ignition (dev) only
- No production error monitoring configured

**Metrics:**
- No application performance monitoring
- No request tracing
- Limited visibility into video processing performance

---

*Concerns analysis: 2026-02-02*

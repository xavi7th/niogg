# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Tech Stack

**Backend:** Laravel 10 (PHP 8.1+) with Nwidart Laravel Modules, Inertia.js, Laravel Sanctum, Ziggy
**Frontend:** Svelte (via Inertia adapter, not SvelteKit), Vite 5, Tailwind CSS 3
**Database:** MariaDB 10 (with Redis for caching/sessions)
**DevOps:** Docker Compose + Laravel Sail, Docker-sync (MacOS optimization)

## Architecture: Modular Monolith

This is NOT a standard Laravel app. Uses **Nwidart Laravel Modules** for modularization. Each module is self-contained with routes, controllers, models, migrations, Svelte components, and tests.

**Active modules** (controlled via `/modules_statuses.json`):

- `UserAuth` - Authentication
- `AppUser` - User management
- `PublicPage` - Public pages (homepage, about, blog)
- `Conference` - Conference management

Module structure:

```
Modules/{ModuleName}/
├── app/Http/Controllers/
├── resources/js/Pages/*.svelte
├── routes/web.php
├── vite.config.js (module-specific)
└── tests/Unit|Feature/
```

**Critical:** Core `/app` directory has minimal logic. Most code lives in modules.

## Common Commands

**Development:**

```bash
# Docker (MacOS with docker-sync optimization)
make start_dev   # Start docker-sync + Sail
make stop_dev    # Stop containers + docker-sync
make kill_dev    # Clean everything for fresh start

# Standard Docker
./vendor/bin/sail up -d
./vendor/bin/sail down

# Frontend dev (includes git hook setup)
npm run dev      # Vite dev server + HMR + pre-commit hooks

# Build for production
npm run build
composer recompile   # Clear caches, optimize Laravel
```

**Testing:**

```bash
./vendor/bin/sail test                    # All tests
./vendor/bin/sail test --filter=TestName  # Single test
./vendor/bin/sail test tests/Unit/        # Test directory
```

**Linting:**

```bash
# PHP
composer lint-check   # Run Pint + PHPCS
composer lint         # Fix with Pint + PHPCBF
vendor/bin/pint       # Laravel Pint only
vendor/bin/phpcs      # PHPCS only

# JS/Svelte
npm run lint          # Check with Prettier + ESLint
npm run format        # Fix with Prettier
```

**Module management:**

```bash
php artisan module:make ModuleName        # Create new module
php artisan module:enable ModuleName      # Enable module
php artisan module:disable ModuleName     # Disable module
```

## Custom Vite Build System

**Module-aware asset loading:** `/vite-module-loader.js` dynamically discovers enabled modules via `modules_statuses.json` and merges their Vite configs at build time.

Each module's `vite.config.js` exports:

```js
export const paths = ["Modules/ModuleName/resources/js/app.js"];
export const aliases = {
  "@modulename-pages": "/Modules/ModuleName/resources/js/Pages",
  "@modulename-components": "/Modules/ModuleName/resources/js/Components",
};
export const concatFiles = [
  /* legacy jQuery files */
];
export const publicFiles = [
  /* static assets to copy */
];
```

**Inertia page resolution:** Supports module namespacing: `PublicPage::Index` → `/Modules/PublicPage/resources/js/Pages/Index.svelte`

**Custom alias pattern:** Each module has prefixed aliases (e.g., `@publicpage-pages`, `@appuser-components`)

## Flash Messages & Notifications

Global flash message handler in `/Modules/PublicPage/resources/js/app.js` intercepts Inertia responses and displays SweetAlert2 notifications. Use Laravel's session flash in controllers:

```php
return redirect()->back()->with('success', 'Message here');
```

## Custom Helpers

`/app/helpers.php` provides:

- `slug_to_string()` - Convert slug to readable string
- `str_ordinal()` - Add ordinal suffix (1st, 2nd, 3rd)
- `is_identical()` - Strict comparison helper
- `parse_size()` - Parse file size strings

## Git Workflow

Pre-commit hooks auto-installed via `npm run dev`. Includes linting and formatting.

**Protected branches:** Cannot commit directly to `master` or `development` (enforced by hooks)

**Current branch:** `development`
**PR target:** `master`

## Development Notes

- **Docker-sync (MacOS only):** Improves volume mount performance. Line must be uncommented in `docker-compose.yml` to use.
- **Modules testing:** Each module has isolated test suites. Main phpunit.xml only covers `/app` directory.
- **Asset concatenation:** Legacy jQuery/plugins concatenated via Rollup plugin to `public/build/assets/` for backward compatibility with existing templates.

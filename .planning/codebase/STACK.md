# Technology Stack

**Analysis Date:** 2026-02-02

## Languages

**Primary:**
- PHP 8.1+ - Backend framework, controllers, and business logic
- JavaScript/TypeScript - Frontend Svelte components and build system

**Secondary:**
- SCSS - CSS preprocessing with Tailwind CSS
- SQL - Database queries (MariaDB)

## Runtime

**Environment:**
- Docker containers with Laravel Sail
- MariaDB 10 database
- Redis for caching/sessions

**Package Managers:**
- Composer (PHP) - `composer.json`
- npm (Node.js) - `package.json`
- PHP version: ^8.1 specified in composer.json

## Frameworks

**Core:**
- Laravel 10.10 - Backend PHP framework
- Inertia.js - Server-side rendering bridge (v1.0+)
- Nwidart Laravel Modules - Modular architecture (11.0.10)
- Svelte - Frontend component framework via Inertia adapter
- Vite 5.4.6 - Frontend build tool and development server
- Tailwind CSS 3.4.11 - Utility-first CSS framework

**Testing:**
- PHPUnit 10.1 - PHP testing framework
- Playwright 1.58.0 - E2E testing
- Laravel Sail - Docker-based development environment

**Build/Dev:**
- Laravel Pint - PHP code formatting
- PHPCS - PHP code standard checking
- Laravel Breeze 1.29 - Authentication scaffolding

## Key Dependencies

**Critical:**
- inertiajs/inertia-laravel (v1.0+) - Server-side rendering
- laravel/sanctum (v3.2+) - API authentication
- tightenco/ziggy (v2.0+) - Route generation in JavaScript
- nwidart/laravel-modules (v11.0.10) - Modular architecture
- intervention/image (v3+) - Image manipulation
- pbmedia/laravel-ffmpeg (v8.7+) - Video processing

**Infrastructure:**
- guzzlehttp/guzzle (v7.2+) - HTTP client
- opcodesio/log-viewer (v3.8) - Log viewing interface

## Configuration

**Environment:**
- Local development via Docker Compose
- Multi-stage development with different ports (8007, 5173)
- Environment variables in `.env` file
- Debug mode enabled for development

**Build:**
- Custom Vite configuration in `/vite.config.js`
- Module-aware asset loading via `/vite-module-loader.js`
- SCSS preprocessor with module-specific imports
- Rollup plugin for asset concatenation

## Platform Requirements

**Development:**
- Docker & Docker Compose
- Node.js (npm)
- PHP 8.1+
- Composer
- Vite dev server on port 5173

**Production:**
- Laravel Sail (Docker containers)
- MariaDB 10
- Redis for caching
- File-based or AWS S3 storage (configured via env vars)

---

*Stack analysis: 2026-02-02*
*Updated from Laravel 10.10 analysis*
```
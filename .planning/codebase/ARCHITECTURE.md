# Architecture

**Analysis Date:** 2026-02-02

## Pattern Overview

**Overall:** Modular Monolith with Domain-Driven Design

**Key Characteristics:**
- Nwidart Laravel Modules for modularization
- Domain separation by business capability
- Inertia.js for server-side rendering with Svelte
- Centralized configuration with module-specific overrides
- Service providers for module registration

## Layers

**Presentation Layer (Frontend):**
- Purpose: Handle UI rendering and user interactions
- Location: `/Modules/{ModuleName}/resources/js/Pages/*.svelte`
- Technology: Svelte with Inertia.js
- Dependencies: Inertia adapter, SweetAlert2 notifications
- Module-specific aliases (e.g., `@publicpage-pages`, `@appuser-components`)

**Application Layer:**
- Purpose: Handle HTTP requests, business logic coordination
- Location: `/Modules/{ModuleName}/app/Http/Controllers/*.php`
- Pattern: Controllers with Form Request validation
- Dependencies: Laravel HTTP kernel, Inertia render()
- Module-specific routes with admin middleware groups

**Domain Layer:**
- Purpose: Core business logic and domain models
- Location: `/Modules/{ModuleName}/app/Models/*.php`
- Pattern: Eloquent models with custom methods
- Dependencies: Laravel ORM, relationships

**Infrastructure Layer:**
- Purpose: External integrations, database, file storage
- Location: `/config`, `/database/migrations`, `/Modules/{ModuleName}/database`
- Patterns: Migration files, factories, seeders
- Dependencies: MariaDB 10, Redis for caching/sessions

## Data Flow

**Request Flow:**

1. HTTP Request → Laravel Router
2. Route module-specific controller
3. Controller processes business logic
4. Inertia renders Svelte component with data
5. Response sent to browser

**Inertia Data Binding:**
```php
// Controller
return Inertia::render('Index', [
    'users' => User::all(),
    'stats' => $statisticsService->get()
]);

// Svelte Component
export default function Index({ users, stats }) {
    // Component logic
}
```

**Asset Loading:**
1. `vite-module-loader.js` scans `modules_statuses.json`
2. Merges Vite configs from enabled modules
3. Builds assets with module-specific aliases
4. Outputs to `public/build/`

## Key Abstractions

**Module Namespace:**
- Purpose: Domain separation and code organization
- Pattern: `Modules\{ModuleName}\`
- Examples: `Modules\PublicPage\Http\Controllers`
- Convention: PSR-4 autoloading per module

**Service Providers:**
- Purpose: Module registration and binding
- Location: `/Modules/{ModuleName}/app/Providers/*.php`
- Pattern: Laravel service provider lifecycle

**Inertia Pages:**
- Purpose: Server-side rendered UI components
- Pattern: Module namespaced (e.g., `PublicPage::Index`)
- Location: `/Modules/{ModuleName}/resources/js/Pages/*.svelte`

## Entry Points

**Web Application:**
- Location: `bootstrap/app.php`
- Triggers: HTTP requests via Laravel router
- Responsibilities: Application instance creation, kernel binding

**API Endpoints:**
- Location: `/Modules/{ModuleName}/routes/api.php`
- Triggers: HTTP API requests
- Responsibilities: RESTful API responses

**Asset Build:**
- Location: `vite.config.js`
- Triggers: `npm run dev/build`
- Responsibilities: Asset compilation, bundling, optimization

**Module Activation:**
- Location: `modules_statuses.json`
- Triggers: Application boot
- Responsibilities: Module enable/disable configuration

## Error Handling

**Strategy:** Centralized with sweetalert2 notifications

**Patterns:**
- Global flash message handler in `PublicPage/resources/js/app.js`
- Inertia intercepts responses for notifications
- Custom SweetAlert2 mixins for different notification types

## Cross-Cutting Concerns

**Logging:**
- Framework: Laravel Monolog
- Patterns: PSR-3 logging, structured logging

**Validation:**
- Framework: Laravel Form Request classes
- Pattern: Module-specific validation rules
- Location: `/Modules/{ModuleName}/app/Http/Requests/*.php`

**Authentication:**
- Framework: Laravel Sanctum
- Pattern: Stateful API tokens
- Location: `/Modules/UserAuth/` module

*Architecture analysis: 2026-02-02*
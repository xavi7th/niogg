# Codebase Structure

**Analysis Date:** 2026-02-02

## Directory Layout

```
[project-root]/
├── .planning/codebase/          # Architecture documentation
├── app/                        # Core Laravel app (minimal logic)
│   ├── Console/Commands/       # Artisan commands
│   ├── Exceptions/             # Exception handlers
│   ├── Http/                   # HTTP layer
│   │   ├── Controllers/        # App-level controllers
│   │   ├── Kernel.php          # HTTP kernel
│   │   ├── Middleware/         # App-level middleware
│   │   └── Requests/           # App-level requests
│   ├── Models/                 # Core models (User)
│   └── Providers/              # App-level providers
├── bootstrap/                  # Laravel bootstrap
│   └── app.php                 # Application entry point
├── config/                     # Laravel configuration
│   ├── app.php                 # App config
│   ├── modules.php             # Module configuration
│   └── ...                     # Other configs
├── database/                   # Database files
│   ├── factories/              # Model factories
│   ├── migrations/             # All migrations (app + modules)
│   └── seeders/                # Database seeders
├── docker/                     # Docker configurations
│   ├── mysql/                  # MySQL-specific configs
│   └── pgsql/                  # PostgreSQL-specific configs
├── Modules/                    # Domain modules
│   ├── UserAuth/               # Authentication
│   ├── AppUser/                # User management
│   ├── PublicPage/             # Public pages & blog
│   └── Conference/             # Conference management
│       ├── app/               # Module app folder
│       ├── database/          # Module database files
│       ├── resources/          # Module resources
│       │   ├── js/            # Frontend assets
│       │   └── views/         # Blade templates
│       └── vite.config.js     # Module Vite config
├── public/                    # Web root
│   ├── build/assets/           # Compiled assets
│   └── modules/                # Module assets URL
├── resources/                  # Global resources
│   └── js/                    # Global JS
├── storage/                    # Laravel storage
├── tests/                      # Tests
├── vendor/                     # Composer dependencies
└── vite.config.js              # Main Vite config
```

## Directory Purposes

**Core Laravel App (`/app`):**

- Purpose: Application-level code shared across modules
- Contains: User model, global controllers, middleware
- Note: Minimal logic, most code lives in modules

**Modules Directory (`/Modules`):**

- Purpose: Domain separation using Nwidart Laravel Modules
- Structure: Each module is self-contained with own app, resources, database
- Active modules: UserAuth, AppUser, PublicPage, Conference
- Controlled via `modules_statuses.json`

**Module Structure (`/Modules/{ModuleName}`):**

```
Modules/ModuleName/
├── app/                       # Module's app directory
│   ├── Http/                  # HTTP layer
│   │   ├── Controllers/       # Module controllers
│   │   ├── Requests/          # Form requests
│   │   └── Middleware/        # Module middleware
│   ├── Models/                # Domain models
│   ├── Policies/              # Authorization policies
│   ├── Providers/             # Module providers
│   └── Transformers/          # API transformers
├── config/                    # Module-specific config
├── database/                  # Migration files
│   ├── factories/             # Model factories
│   └── migrations/            # Database migrations
├── resources/                 # Frontend resources
│   ├── js/                    # Svelte components & JS
│   │   ├── Pages/             # Inertia pages
│   │   ├── Components/        # Shared components
│   │   └── Partials/          # UI partials
│   ├── views/                 # Blade templates
│   ├── sass/                  # SCSS files
│   └── images/                # Static images
├── routes/                    # Module routes
│   ├── web.php                # Web routes
│   └── api.php                # API routes
└── vite.config.js             # Module-specific Vite config
```

**Docker (`/docker`):**

- Purpose: Containerization with Laravel Sail
- Contains: Docker configurations for PHP 7.3-8.3, MySQL, PostgreSQL
- Used: Local development and deployment

## Key File Locations

**Entry Points:**

- `bootstrap/app.php`: Application instance creation
- `vite.config.js`: Asset build configuration
- `modules_statuses.json`: Module activation control

**Configuration:**

- `config/modules.php`: Module configuration
- `app/Http/Kernel.php`: HTTP middleware registration
- `config/app.php`: Application configuration

**Core Logic:**

- `Modules/{ModuleName}/app/Http/Controllers/`: Controllers
- `Modules/{ModuleName}/app/Models/`: Domain models
- `Modules/{ModuleName}/routes/`: Route definitions

**Frontend:**

- `Modules/{ModuleName}/resources/js/Pages/`: Svelte pages
- `Modules/{ModuleName}/vite.config.js`: Module asset config

## Naming Conventions

**Files:**

- Controllers: PascalCase, suffix `Controller` (e.g., `PublicBlogController`)
- Models: PascalCase, singular (e.g., `User`, `Conference`)
- Views: PascalCase, descriptive (e.g., `Dashboard.blade.php`)
- Routes: kebab-case for URLs, snake_case for route names
- Pages: PascalCase (e.g., `Index.svelte`, `UserProfile.svelte`)

**Directories:**

- Modules: PascalCase (e.g., `PublicPage`, `UserAuth`)
- Controllers: PascalCase, descriptive (e.g., `AdminEventController`)
- Views: kebab-case (e.g., `user-profile`)
- Tests: PascalCase, `Test` suffix (e.g., `UserControllerTest`)

## Where to Add New Code

**New Module:**

- Create: `php artisan module:make ModuleName`
- Enable: Add to `modules_statuses.json`
- Structure: Follow existing module pattern

**New Feature in Existing Module:**

- Controller: `/Modules/{ModuleName}/app/Http/Controllers/`
- Views: `/Modules/{ModuleName}/resources/views/`
- Frontend: `/Modules/{ModuleName}/resources/js/Pages/`
- Routes: `/Modules/{ModuleName}/routes/web.php`

**New API Endpoint:**

- Controller: `/Modules/{ModuleName}/app/Http/Controllers/`
- Routes: `/Modules/{ModuleName}/routes/api.php`
- Validation: `/Modules/{ModuleName}/app/Http/Requests/`

**New Component/Module:**

- Implementation: `/Modules/{ModuleName}/resources/js/Components/`
- Update: `/Modules/{ModuleName}/vite.config.js` with new aliases
- Import: Use module-specific aliases (e.g., `@publicpage-components`)

**Shared Helpers:**

- Global helpers: `/app/helpers.php`
- Module helpers: `/Modules/{ModuleName}/app/Helpers/` (if created)

## Special Directories

**`/Modules`:**

- Purpose: Domain-separated modules
- Generated: No, created manually
- Committed: Yes

**`/storage` (within modules):**

- Purpose: Module-specific storage
- Generated: No, user uploads
- Committed: No, in .gitignore

**`/public/modules`:**

- Purpose: URL-accessible module assets
- Generated: Yes, by Vite build
- Committed: No, build artifacts

_Structure analysis: 2026-02-02_

# NIOGG Website & Conference Platform

This is the official website and conference management platform for **NIOGG (Nigeria Initiative for Good Governance)**, a nonprofit civic organization dedicated to promoting good governance in Nigeria.

## About NIOGG

NIOGG is committed to advancing good governance in Nigeria through:

- **Youth Empowerment** - Leadership training and development programs for Nigerian youth
- **Judicial Independence** - Advocating for transparent, independent judicial systems and anti-corruption efforts
- **Poverty Eradication** - Skills training and economic empowerment initiatives for marginalized communities
- **Resource Management** - Promoting sustainable practices and transparent policies for natural resource stewardship

### Core Activities

NIOGG demonstrates the benefits of good governance through:

- **Entrepreneurship Training** - Practical workshops and mentorship for business creation
- **Free Medical Treatments** - Healthcare services for underserved communities
- **Charity Initiatives** - Direct support for vulnerable populations
- **Awards & Recognition Programs** - Honoring individuals with outstanding contributions to community development

## Website Features

- **Public Information** - Homepage, About Us, Team profiles, Vision & Values
- **Blog** - Articles and updates on governance, civic engagement, and initiatives
- **Conference Platform** - Conference registration and event management
- **Community Engagement** - Contact forms, testimonials, partner showcases
- **Job Opportunities** - Careers page for organizational roles
- **Media Gallery** - Photos and visual documentation of events and initiatives

## Tech Stack

- **Backend:** Laravel 10 with modular architecture (Nwidart Laravel Modules)
- **Frontend:** Svelte + Inertia.js for dynamic page rendering
- **Build Tool:** Vite 5
- **Styling:** Tailwind CSS
- **Database:** MariaDB
- **Caching:** Redis
- **Containerization:** Docker + Laravel Sail
- **Authentication:** Laravel Sanctum

## Project Structure

### 1. Project Type and Tech Stack

**Backend:**

- Laravel 10 (PHP 8.1+)
- MariaDB 10
- Redis (caching/sessions)
- Inertia.js for server-client bridge
- Laravel Sanctum (authentication)
- Ziggy (route helper)

**Frontend:**

- Svelte (not SvelteKit - using Inertia adapter)
- Vite 5.4.6 (build tool)
- Tailwind CSS 3.4.11
- SweetAlert2 (notifications)
- Lucide Svelte (icons)
- Bits-ui, Vaul-svelte (UI components)

**Development Tools:**

- Docker/Laravel Sail (containerization)
- Docker-sync (MacOS performance optimization)
- PHPUnit (testing)
- Laravel Pint & PHPCS (PHP linting)
- Prettier & ESLint (JS/Svelte linting)
- Larastan (static analysis)
- Laravel Log Viewer

### 2. Overall Architecture

**Type: Modular Monolith**

Uses Nwidart Laravel Modules package for modularization. Each module is self-contained with:

- Controllers, Models, Policies
- Routes (web/api)
- Migrations, Factories, Seeders
- Svelte components
- Module-specific Vite configs
- Tests

**Active Modules:**

1. **UserAuth** - Authentication system
2. **AppUser** - User management/profiles
3. **PublicPage** - Public-facing pages (homepage, about, contact, blog)
4. **Conference** - Conference management

Module activation controlled via `/modules_statuses.json`.

### 3. Key Directories and Purposes

**Core Laravel:**

- `/app` - Core application logic (minimal, most logic in modules)
- `/config` - Laravel configuration
- `/database/migrations` - Shared migrations
- `/routes` - Empty (routes in modules)
- `/resources` - Minimal (assets in modules)

**Modules Structure:**

```
/Modules/{ModuleName}/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Policies/
│   ├── Providers/
│   ├── DTOs/
│   └── Transformers/
├── config/
├── database/
├── resources/
│   ├── js/Pages/*.svelte
│   ├── sass/
│   ├── images/
│   └── template/ (vendor templates)
├── routes/
├── tests/
├── vite.config.js
├── package.json
└── module.json
```

**Docker:**

- `/docker/` - Multiple PHP versions (8.0-8.3), MySQL, PostgreSQL configs
- `docker-compose.yml` - MariaDB, Redis, Mailpit, Soketi (websockets)
- `docker-sync.yml` - Volume sync for MacOS

### 4. Build System and Tooling

**Custom Module-Aware Vite Setup:**

- `/vite-module-loader.js` - Dynamically loads enabled modules' Vite configs
- Each module exports: paths, aliases, concatFiles, publicFiles
- Main Vite config merges all module configs
- Supports:
  - Dynamic imports of Svelte components across modules
  - Module-specific path aliases (e.g., `@publicpage-pages`)
  - Asset concatenation (jQuery, plugins)
  - Static file copying (images, fonts)

**Inertia Page Resolution:**

- Custom resolver supports module namespacing: `ModuleName::PagePath`
- Example: `PublicPage::Index` resolves to `/Modules/PublicPage/resources/js/Pages/Index.svelte`

**Scripts:**

- `npm run dev` - Vite dev server + git hooks setup
- `npm run build` - Production build
- `composer recompile` - Clear caches, optimize Laravel
- `make start_dev` - Docker-sync + Sail (MacOS optimized)

### 5. Testing Setup

**PHPUnit Configuration:**

- Test suites: Unit, Feature
- Coverage: `/app` directory only (modules tested separately)
- Test database: separate `testing` database

**Per-Module Testing:**

- Each module has `/tests/Unit` and `/tests/Feature`
- Isolated test environments per module

### Architecture Highlights

**Non-Obvious Design Patterns:**

1. **Module Discovery System:** Vite dynamically discovers and loads module assets at build time by reading `modules_statuses.json`

2. **Hybrid Routing:** Routes defined in individual modules but registered globally through module service providers

3. **Asset Pipeline:**
   - Legacy jQuery templates concatenated via Rollup plugin
   - Modern Svelte components bundled via Vite
   - Static assets copied to public build directory

4. **Flash Message System:** Global router event handlers in `/Modules/PublicPage/resources/js/app.js` intercept Inertia responses and display SweetAlert2 notifications

5. **Development Workflow:**
   - Pre-commit hooks auto-copy on `npm run dev`
   - Docker-sync for MacOS to avoid volume mount performance issues
   - Module hot-reloading via Vite HMR

6. **Custom Helpers:** `/app/helpers.php` provides utility functions (slug_to_string, str_ordinal, is_identical, parse_size)

This is a well-architected Laravel application using a modular monolith pattern with modern frontend tooling (Vite + Svelte) integrated via Inertia.js, designed for both containerized and traditional development environments.

## Getting Started

See [CLAUDE.md](CLAUDE.md) for development guidance.

### Prerequisites

- Docker & Docker Compose (or PHP 8.1+, Node.js, MariaDB)
- Composer
- npm or bun

### Installation

```bash
# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations (with Docker)
./vendor/bin/sail artisan migrate

# Start development server (with Docker)
make start_dev      # macOS with docker-sync
# or
./vendor/bin/sail up -d

# Start frontend dev server
npm run dev
```

### Development Commands

```bash
# Build for production
npm run build
composer recompile

# Run tests
./vendor/bin/sail test

# Linting & formatting
composer lint-check   # Check code style
npm run lint          # Check JS/Svelte

# Create new module
php artisan module:make ModuleName
```

## Deployment

### Overview

Two deployment strategies are available:

#### 1. Rsync Strategy (Recommended)

Uses `rsync` with atomic symlink switching for production deployments. **Recommended for robustness and safety.**

**Advantages:**

- Separates deployment from VCS concerns
- Efficient incremental transfers (only changed files synced)
- Atomic release switching via symlinks (zero-downtime)
- Easy rollbacks (previous releases remain on disk)
- Supports dry-run mode for safe testing
- Proper release lifecycle management (automatic cleanup)
- Health check validation post-deployment
- No git state manipulation

**Disadvantages:**

- Requires rsync on both local and remote machines
- Needs SSH access and rsync installed on server

**Setup:**

1. Configure your server details in `deploy.sh`:

```bash
# Edit deploy.sh and update:
SSH_ALIAS="niogg-server"           # Your SSH alias/host
BASE="/home/user/niogg.org"        # Base directory on server
HEALTH_CHECK_URL="https://niogg.org"
```

2. Ensure server directory structure exists:

```bash
ssh niogg-server mkdir -p /home/user/niogg.org/shared/{env,storage,vendor}
ssh niogg-server mkdir -p /home/user/niogg.org/releases
```

3. Place `.env` file in shared location:

```bash
scp .env.production niogg-server:/home/user/niogg.org/shared/env/.env
```

**Deploy:**

```bash
# Standard deployment (staging or production)
./deploy.sh staging
./deploy.sh production

# Dry-run mode (test without making changes)
DEPLOY_DRY_RUN=1 ./deploy.sh production

# Skip build (if already built locally)
SKIP_BUILD=1 ./deploy.sh production

# Customize release retention
KEEP_RELEASES=10 ./deploy.sh production
```

**Rollback:**

```bash
ssh niogg-server
cd /home/user/niogg.org
ln -sfn releases/<previous-release-date> current
ln -sfn current/public public
```

#### 2. Git Push Strategy (Legacy)

Uses `deploy.js` to commit build artifacts to git and force-push to a production remote.

**Advantages:**

- Build history tracked in git
- Simple conceptually

**Disadvantages:**

- Pollutes git history with build commits
- Requires force-push to production remote (risky)
- Modifies .gitignore dynamically (potential corruption)
- No rollback mechanism (need to revert commits)
- Complex stash/pop logic for local changes
- Build output mixed with source code in VCS
- No zero-downtime capability

**Deploy:**

```bash
npm run push -- production
npm run push -- staging
```

**Note:** This approach is less robust and should only be used if rsync setup isn't feasible.

### Pre-Deployment Checklist

- [ ] All tests pass: `./vendor/bin/sail test`
- [ ] Code linting passes: `composer lint-check && npm run lint`
- [ ] Build succeeds: `npm run build && composer recompile`
- [ ] `.env` file configured for target environment
- [ ] Database backups are recent
- [ ] No uncommitted changes on deployment branch

### Contributing

To contribute to NIOGG's website and platform, please follow the development guidelines in [CLAUDE.md](CLAUDE.md). Ensure all code passes linting and tests before submitting pull requests.

Commit directly to `master` and `development` branches is protected. Work on feature branches and submit pull requests.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

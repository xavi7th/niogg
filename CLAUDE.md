# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Tech Stack

**Backend:** Laravel 10 (PHP 8.1+) with Nwidart Laravel Modules, Inertia.js, Laravel Sanctum, Ziggy
**Frontend:** Svelte (via Inertia adapter, not SvelteKit), Vite 5, Tailwind CSS 3
**Database:** MariaDB 10 (with Redis for caching/sessions)
**DevOps:** Docker Compose + Laravel Sail

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

**Unified dev server (recommended):**
```bash
composer dev      # Docker + Vite + queue + logs + scheduler — all in one
```

**Manual Docker startup (backup):**
```bash
make start_dev   # Containers only (detached)
make watch_dev   # Containers (foreground) + log tail
make stop_dev    # Stop containers
make kill_dev    # Clean everything for fresh start
```

**Frontend:**
```bash
bun run dev      # Vite dev server + HMR + pre-commit hooks
bun run build    # Build for production
```

**Build for production:**
```bash
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

- **Modules testing:** Each module has isolated test suites. Main phpunit.xml only covers `/app` directory.
- **Asset concatenation:** Legacy jQuery/plugins concatenated via Rollup plugin to `public/build/assets/` for backward compatibility with existing templates.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5.1
- inertiajs/inertia-laravel (INERTIA) - v0
- laravel/framework (LARAVEL) - v10
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v3
- tightenco/ziggy (ZIGGY) - v2
- larastan/larastan (LARASTAN) - v2
- laravel/breeze (BREEZE) - v1
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v10

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `vendor/bin/sail npm run build`, `vendor/bin/sail npm run dev`, or `vendor/bin/sail composer run dev`. Ask them.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
  - <code-snippet>public function \_\_construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

### Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks

- Add useful array shape type definitions for arrays when appropriate.

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== sail rules ===

## Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
  - Run Artisan Commands: `vendor/bin/sail artisan migrate`
  - Install Composer packages: `vendor/bin/sail composer install`
  - Execute Node commands: `vendor/bin/sail npm run dev`
  - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `vendor/bin/sail artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

## Inertia

- Inertia.js components should be placed in the `resources/js/Pages` directory unless specified differently in the JS bundler (`vite.config.js`).
- Use `Inertia::render()` for server-side routing instead of traditional Blade views.
- Use the `search-docs` tool for accurate guidance on all things Inertia.

<code-snippet name="Inertia Render Example" lang="php">
// routes/web.php example
Route::get('/users', function () {
    return Inertia::render('Users/Index', [
        'users' => User::all()
    ]);
});
</code-snippet>

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `vendor/bin/sail artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

=== laravel/v10 rules ===

## Laravel 10

- Use the `search-docs` tool to get version-specific documentation.
- Middleware typically live in `app/Http/Middleware/` and service providers in `app/Providers/`.
- Laravel 10 has a `bootstrap/app.php` file that creates the application instance and binds kernel contracts, but does not use it for application configuration like Laravel 11:
  - Middleware registration is in `app/Http/Kernel.php`
  - Exception handling is in `app/Exceptions/Handler.php`
  - Console commands and schedule registration is in `app/Console/Kernel.php`
  - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`
- When using Eloquent model casts, you must use `protected $casts = [];` and not the `casts()` method. The `casts()` method isn't available on models in Laravel 10.

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/sail bin pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test`, simply run `vendor/bin/sail bin pint` to fix any formatting issues.

=== phpunit/core rules ===

## PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `vendor/bin/sail artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

### Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `vendor/bin/sail artisan test --compact`.
- To run all tests in a file: `vendor/bin/sail artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `vendor/bin/sail artisan test --compact --filter=testName` (recommended after making a change to a related file).
  </laravel-boost-guidelines>

# Testing Patterns

**Analysis Date:** 2026-02-02

## Test Framework

**Runner:**

- PHPUnit 10
- Laravel's artisan test command
- Config: `/phpunit.xml`

**Assertion Library:**

- PHPUnit's built-in assertions
- Laravel's test helpers (`assertSessionHas`, `assertDatabaseHas`, etc.)

**Run Commands:**

```bash
# All tests
vendor/bin/sail artisan test --compact

# Specific test directory
vendor/bin/sail artisan test tests/Feature

# Single test file
vendor/bin/sail artisan test tests/Feature/EventHeaderDisplayTest.php

# Filter by test name
vendor/bin/sail artisan test --filter=test_media_showcase_page_loads
```

## Test File Organization

**Location:**

- Feature tests: `tests/Feature/`
- Unit tests: `tests/Unit/`
- Module tests: `Modules/{ModuleName}/tests/`
- Module tests are isolated and don't run in main phpunit.xml

**Naming:**

- Feature tests: `[Feature]Test.php` (e.g., `EventHeaderDisplayTest.php`)
- Unit tests: `[Unit]Test.php` (e.g., `VideoServiceTest.php`)
- E2E tests: `[Feature]Test.php` in E2E directory

**Structure:**

```
Modules/
└── PublicPage/
    └── tests/
        ├── Feature/
        │   ├── Admin/
        │   │   ├── AdminEventControllerTest.php
        │   │   └── AdminDashboardControllerTest.php
        │   ├── EventHeaderDisplayTest.php
        │   └── ResponsiveDesignTest.php
        ├── E2E/
        │   ├── LazyLoadingTest.php
        │   └── ResponsiveAccessibilityTest.php
        └── Unit/
            ├── Services/
            │   └── VideoUploadServiceTest.php
            └── Models/
                └── EventTest.php
```

## Test Structure

**Suite Organization:**

```php
class AdminEventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get('/admin/events');
        $response->assertRedirect('/login');
    }

    public function test_index_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->get('/admin/events');
        $response->assertStatus(403);
    }
}
```

**Patterns:**

- Use `RefreshDatabase` trait for feature tests
- Test both happy and failure paths
- Include authentication/authorization tests
- Test cache invalidation
- Test database transactions and cascading deletes
- Use Inertia assertions for SPA components

## Mocking

**Framework:** PHPUnit's built-in mocking

**Patterns:**

- Mock external services (e.g., Storage, Cache)
- Mock Jobs for async operations
- Use facades for service mocking
- Example:

```php
Storage::shouldReceive('disk')
    ->with('public')
    ->andReturn($mockStorage);

Cache::shouldReceive('forget')
    ->with($cacheKey)
    ->once();
```

**What to Mock:**

- File system operations
- External API calls
- Queue jobs
- Cache operations
- Time-sensitive functions

**What NOT to Mock:**

- Eloquent relationships
- Laravel facades that don't need isolation
- Database transactions (handled by RefreshDatabase)

## Fixtures and Factories

**Test Data:**

```php
Event::create([
    'name' => 'Community Impact Program: Free Medical Outreach',
    'description' => 'Our annual medical outreach program...',
    'icon' => '🏥',
    'category' => 'charity_event',
    'event_date' => now()->addMonths(2),
    'slug' => 'charity-event',
    'is_published' => TRUE,
]);
```

**Location:**

- Factories: `Modules/{ModuleName}/database/factories/`
- Seeders: `Modules/{ModuleName}/database/seeders/`

**Factory States:**

```php
class EventFactory extends Factory
{
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => TRUE,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => FALSE,
        ]);
    }
}

// Usage in tests
Event::factory()->published()->count(5)->create();
```

## Coverage

**Requirements:** No enforced minimum coverage requirement in main phpunit.xml

**View Coverage:**

```bash
# Generate coverage report
vendor/bin/sail php vendor/bin/phpunit --coverage-html coverage

# Generate clover format
vendor/bin/sail php vendor/bin/phpunit --coverage-clover coverage.xml
```

**Exclusions:**

- Vendor files
- Generated files
- Test files themselves
- Configuration files

## Test Types

**Unit Tests:**

- Test business logic in isolation
- Mock external dependencies
- Test small, focused pieces of code
- Example: Service methods, validation rules
- Location: `tests/Unit/`

**Feature Tests:**

- Test HTTP endpoints
- Test complete user flows
- Test database interactions
- Test authentication/authorization
- Use `RefreshDatabase` trait
- Location: `tests/Feature/`

**E2E Tests:**

- Test complete user journeys
- Test JavaScript functionality
- Test responsive design
- Test accessibility
- Use Playwright for browser automation
- Location: `tests/E2E/`

## Common Patterns

**Async Testing:**

```php
public function test_bulk_publish_publishes_events(): void
{
    $user = User::factory()->create(['is_admin' => TRUE]);
    $event1 = Event::factory()->create(['is_published' => FALSE]);
    $event2 = Event::factory()->create(['is_published' => FALSE]);

    $response = $this->actingAs($user)->postJson('/admin/events/bulk/publish', [
        'event_ids' => [$event1->id, $event2->id],
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => '2 event(s) published successfully.',
            'count' => 2,
        ]);
}
```

**Error Testing:**

```php
public function test_bulk_publish_requires_event_ids_array(): void
{
    $user = User::factory()->create(['is_admin' => TRUE]);

    $response = $this->actingAs($user)->postJson('/admin/events/bulk/publish', [
        'event_ids' => 'not-an-array',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['event_ids']);
}
```

**Cache Testing:**

```php
public function test_index_caches_paginated_results(): void
{
    $user = User::factory()->create(['is_admin' => TRUE]);
    Event::factory()->count(20)->create();

    Cache::flush();
    $this->actingAs($user)->get('/admin/events?page=1');

    $cacheKey = 'admin.events.list:page:1:per_page:15';
    $this->assertNotNull(Cache::tags(['admin.events'])->get($cacheKey));
}
```

**Authorization Testing:**

```php
public function test_index_requires_authentication(): void
{
    $response = $this->get('/admin/events');
    $response->assertRedirect('/login');
}

public function test_index_requires_admin_role(): void
{
    $user = User::factory()->create(['is_admin' => FALSE]);
    $response = $this->actingAs($user)->get('/admin/events');
    $response->assertStatus(403);
}
```

**Inertia Testing:**

```php
$response->assertInertia(function ($page) use ($event): void {
    $page->where('event.id', $event->id)
        ->has('event.videos', 4);
});
```

## Testing Best Practices

- Test happy paths first
- Test edge cases and error conditions
- Use descriptive test names
- Follow AAA pattern (Arrange-Act-Assert)
- Keep tests simple and focused
- Use factories for test data
- Reset database between tests
- Test cache invalidation when applicable
- Test both API and web routes

---

_Testing analysis: 2026-02-02_

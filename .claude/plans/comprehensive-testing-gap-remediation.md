# Comprehensive Testing Gap Remediation Plan

---

## SESSION STATE (UPDATE AFTER EACH SESSION)

```
CURRENT_PHASE: 0
STATUS: not started
LAST_UPDATED: 2026-05-21
```

### Phase Progress Table

| Phase | Title | Status | Date | Notes |
|-------|-------|--------|------|-------|
| 1 | Infrastructure: phpunit.xml, composer scripts, .env.testing | ⏳ | — | |
| 2 | Factory enhancements: User admin states, EventPhoto states, Video helpers | ⏳ | — | |
| 3 | Test utility traits: InteractsWithAuthentication, InteractsWithInertia, RefreshResponseCache | ⏳ | — | |
| 4 | Quality fix: Delete/rewrite all `assertTrue(TRUE)` no-op tests | ⏳ | — | |
| 5 | Quality fix: Refactor setUp() methods to use factories | ⏳ | — | |
| 6 | Quality fix: Rename/move misclassified E2E tests | ⏳ | — | |
| 7 | UserAuth module: Controllers (login, logout, password reset, email verification) | ⏳ | — | |
| 8 | UserAuth module: LoginRequest rate limiting, Notifications, EventSubscriber | ⏳ | — | |
| 9 | AppUser module: Profile controller, dashboard, ProfileUpdateRequest | ⏳ | — | |
| 10 | PublicPage: PublicPageController (home, about, contact, gallery) | ⏳ | — | |
| 11 | PublicPage: VideoUploadService unit tests | ⏳ | — | |
| 12 | PublicPage: EventPhotoUploadService unit tests | ⏳ | — | |
| 13 | PublicPage: Queue jobs (ConvertVideoToMp4, GenerateVideoThumbnail, GeneratePhotoThumbnail) | ⏳ | — | |
| 14 | PublicPage: Console commands (CreateAdminUser, RetryFailedVideoThumbnails, RetryFailedThumbnails) | ⏳ | — | |
| 15 | PublicPage: Models (User, Event, Video, EventPhoto) unit tests | ⏳ | — | |
| 16 | PublicPage: Middleware (IsAdmin, HandleInertiaRequests) | ⏳ | — | |
| 17 | PublicPage: Form Requests (VideoUploadRequest, VideoFormRequest, StoreEventPhotosRequest) | ⏳ | — | |
| 18 | PublicPage: DTOs, Emails, Notifications | ⏳ | — | |
| 19 | PublicPage: Missing edge cases in existing admin controller tests | ⏳ | — | |
| 20 | PublicPage: Conference module tests | ⏳ | — | |
| 21 | CI/CD: GitHub Actions workflow | ⏳ | — | |
| 22 | Playwright: Actual E2E test scaffolding | ⏳ | — | |

---

## Architecture Overview

```
Testing Structure After Plan:

tests/
├── TestCase.php                          (updated with base helpers)
├── CreatesApplication.php                (unchanged)
├── Concerns/                             (NEW)
│   ├── InteractsWithAuthentication.php   (signInAsAdmin, signInAsSuperAdmin)
│   ├── InteractsWithInertia.php          (assertInertiaComponent, etc.)
│   └── RefreshResponseCache.php          (cache cleanup helper)
├── Feature/
│   ├── Auth/                             (existing Breeze tests - unchanged)
│   ├── UserAuth/                         (NEW - login, password reset, email verification)
│   ├── AppUser/                          (NEW - profile, dashboard)
│   ├── Middleware/                       (NEW - IsAdmin, HandleInertiaRequests)
│   └── DatabaseIndexTest.php             (existing)
├── Unit/
│   ├── Models/                           (NEW - User, Event, Video, EventPhoto)
│   ├── Services/                         (NEW - VideoUploadService, EventPhotoUploadService)
│   ├── Jobs/                             (NEW - ConvertVideoToMp4, etc.)
│   ├── Commands/                         (NEW - CreateAdminUser, etc.)
│   ├── DTOs/                             (NEW - ContactFormMessageDTO)
│   ├── Emails/                           (NEW - NewContactFormMessage)
│   └── Notifications/                    (NEW - UserAuth notifications)
└── E2E/                                  (NEW - Playwright .spec.ts files)

Modules/PublicPage/tests/
├── Feature/
│   ├── Admin/                            (existing + new edge cases)
│   ├── PublicPages/                      (NEW - public controller tests)
│   ├── FormRequests/                     (existing + new)
│   └── ...                               (existing, refactored setUp)
└── E2E/                                  (MOVED from Feature - PHP HTTP tests renamed)
```

### Safety: Existing Tests NOT Modified Destructively

| Category | Action | Reason |
|----------|--------|--------|
| Existing passing tests | Refactor setUp() only | Use factories, keep assertions intact |
| Breeze auth tests | Unchanged | Already cover basic auth flow |
| Admin controller tests | Add new tests only | Don't modify existing passing tests |
| `assertTrue(TRUE)` tests | Delete or rewrite | Zero-value assertions removed |
| E2E PHP tests | Move + rename | Not actual Playwright tests |

---

## Critical Questions — All Answered ✅

| # | Question | Answer |
|---|----------|--------|
| 1 | Where do new root-level tests go? | ✅ `tests/Feature/` and `tests/Unit/` with subdirectories per domain |
| 2 | Where do module tests go? | ✅ Stay in `Modules/PublicPage/tests/` — follow existing convention |
| 3 | Factory states vs manual creation? | ✅ All setUp() refactored to use factories with states |
| 4 | What to do with `assertTrue(TRUE)` tests? | ✅ Delete if no real behavior to test, rewrite if behavior exists |
| 5 | E2E PHP tests — keep or move? | ✅ Move to `Feature/` (they're HTTP tests), rename to remove "E2E" |
| 6 | Playwright E2E — actual browser tests? | ✅ Scaffold real Playwright `.spec.ts` files with 2-3 critical path tests |
| 7 | Database for tests? | ✅ Continue using MariaDB via Sail (existing setup) |
| 8 | Code coverage tool? | ✅ PHPUnit built-in coverage (no extra dependency needed) |
| 9 | CI/CD platform? | ✅ GitHub Actions (standard, free for public repos) |
| 10 | Test naming convention? | ✅ snake_case method names, `@test` annotation removed |
| 11 | PHPUnit parallel? | ✅ Add `--parallel` support via brianium/paratest if beneficial |
| 12 | SQLite fallback for unit tests? | ✅ Not needed — MariaDB is fast enough via Sail, keep single DB |

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Implement phase by phase in strict order. After each phase, update the progress table above.

### Rules — follow without exception

1. **Implement phases in strict order** (Phase 1 → 2 → 3 → ... → 22). Never skip ahead.
2. **Use the exact code provided.** Copy verbatim. Only adapt the parts the plan explicitly says to adapt.
3. **Do not create any file not listed in this plan.**
4. **Run every bash command exactly as written.**
5. **All commands run from repo root** (`/Users/leinad/Work/htdocs/asuke-niogg.org/`).
6. **Run `vendor/bin/sail bin pint` after each phase** to match project code style.
7. **Run the affected tests after each phase** to verify they pass: `vendor/bin/sail artisan test --compact --filter=phaseRelevantFilter`
8. **Every test MUST use factories** — no `Model::create()` with manual data in setUp().
9. **Every test MUST cover happy path + at least 2 failure scenarios** where applicable.
10. **ALL code MUST use 2-space indentation.** Never use 4-space indentation. This applies to PHP, Svelte, TypeScript, JavaScript, and all other files. The project's Pint and Prettier configs enforce 2 spaces — follow this without exception.

---

## PHASE 1 — Infrastructure: phpunit.xml, composer scripts, .env.testing

**Goal:** Fix testing infrastructure configuration gaps.

### Step 1.1 — Update phpunit.xml

Edit `phpunit.xml`. Replace entire file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         displayDetailsOnTestsThatTriggerWarnings="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
            <directory>Modules/PublicPage/tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">app</directory>
            <directory suffix=".php">Modules/PublicPage/app</directory>
            <directory suffix=".php">Modules/UserAuth/app</directory>
            <directory suffix=".php">Modules/AppUser/app</directory>
            <directory suffix=".php">Modules/Conference/app</directory>
        </include>
        <exclude>
            <directory suffix=".php">app/Providers</directory>
            <directory suffix=".php">Modules/*/app/Providers</directory>
        </exclude>
    </source>
    <coverage>
        <report>
            <html outputDirectory="coverage/html" lowUpperBound="50" highLowerBound="80"/>
            <clover outputFile="coverage/clover.xml"/>
        </report>
    </coverage>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_DATABASE" value="testing"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="PULSE_ENABLED" value="false"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

Key changes:
- Added `Modules/*/tests/Feature` to Feature testsuite
- Added all module `app/` directories to source coverage
- Excluded Provider directories from coverage
- Added `<coverage>` block with HTML and Clover outputs
- Added `displayDetailsOnTestsThatTriggerWarnings` and `displayDetailsOnTestsThatTriggerDeprecations`

### Step 1.2 — Create coverage directory

```bash
mkdir -p coverage
```

Add to `.gitignore` (append if file exists):

```
/coverage/
```

### Step 1.3 — Update composer.json scripts

Edit `composer.json`. Replace the `"test"` script and add new scripts:

```json
"test": [
    "./scripts/test.sh"
],
"test-coverage": [
    "./vendor/bin/sail php vendor/bin/phpunit --coverage-html=coverage/html --coverage-clover=coverage/clover.xml"
],
"test-compact": [
    "./vendor/bin/sail artisan test --compact"
],
"test-feature": [
    "./vendor/bin/sail artisan test --compact --testsuite=Feature"
],
"test-unit": [
    "./vendor/bin/sail artisan test --compact --testsuite=Unit"
],
```

### Step 1.4 — Create .env.testing

Create `.env.testing`:

```
APP_NAME="Asuke Niogg (Testing)"
APP_ENV=testing
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=testing
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_DRIVER=array
MAIL_MAILER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array

BCRYPT_ROUNDS=4
```

Note: The `APP_KEY` should match the development `.env` value. Copy it from `.env`.

### Step 1.5 — Create coverage .gitignore

Create `coverage/.gitignore`:

```
*
!.gitignore
```

---

## PHASE 2 — Factory Enhancements

**Goal:** Add missing factory states so tests can use expressive factory patterns.

### Step 2.1 — Update UserFactory

Edit `database/factories/UserFactory.php`. Add states:

```php
public function admin(): static
{
  return $this->state(fn (array $attributes) => [
    'is_admin' => true,
    'is_super_admin' => false,
  ]);
}

public function superAdmin(): static
{
  return $this->state(fn (array $attributes) => [
    'is_admin' => true,
    'is_super_admin' => true,
  ]);
}
```

### Step 2.2 — Update EventFactory

Edit `Modules/PublicPage/database/factories/EventFactory.php`. Add category states:

```php
public function charityEvent(): static
{
  return $this->state(fn (array $attributes) => [
    'category' => 'Charity Drive',
  ]);
}

public function conferenceEvent(): static
{
  return $this->state(fn (array $attributes) => [
    'category' => 'Conference',
  ]);
}

public function protestEvent(): static
{
  return $this->state(fn (array $attributes) => [
    'category' => 'Protests',
  ]);
}
```

### Step 2.3 — Update VideoFactory

Edit `Modules/PublicPage/database/factories/VideoFactory.php`. Add helper state:

```php
public function forEvent(\Modules\PublicPage\Models\Event $event): static
{
  return $this->state(fn (array $attributes) => [
    'event_id' => $event->id,
  ]);
}

public function withDuration(int $seconds): static
{
  return $this->state(fn (array $attributes) => [
    'duration_seconds' => $seconds,
  ]);
}
```

### Step 2.4 — Update EventPhotoFactory

Edit `Modules/PublicPage/database/factories/EventPhotoFactory.php`. Add sortOrder state:

```php
public function sortOrder(int $order): static
{
  return $this->state(fn (array $attributes) => [
    'sort_order' => $order,
  ]);
}
```

---

## PHASE 3 — Test Utility Traits

**Goal:** Create reusable test traits to reduce duplication.

### Step 3.1 — Create tests/Concerns/InteractsWithAuthentication.php

Create `tests/Concerns/InteractsWithAuthentication.php`:

```php
<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait InteractsWithAuthentication
{
  use RefreshDatabase;

  protected function createAdminUser(array $overrides = []): User
  {
    return User::factory()->admin()->create($overrides);
  }

  protected function createSuperAdminUser(array $overrides = []): User
  {
    return User::factory()->superAdmin()->create($overrides);
  }

  protected function createRegularUser(array $overrides = []): User
  {
    return User::factory()->create(array_merge([
      'is_admin' => false,
      'is_super_admin' => false,
    ], $overrides));
  }

  protected function signInAsAdmin(): User
  {
    $user = $this->createAdminUser();
    $this->actingAs($user);
    return $user;
  }

  protected function signInAsSuperAdmin(): User
  {
    $user = $this->createSuperAdminUser();
    $this->actingAs($user);
    return $user;
  }

  protected function signInAsRegularUser(): User
  {
    $user = $this->createRegularUser();
    $this->actingAs($user);
    return $user;
  }
}
```

### Step 3.2 — Create tests/Concerns/InteractsWithInertia.php

Create `tests/Concerns/InteractsWithInertia.php`:

```php
<?php

namespace Tests\Concerns;

use Illuminate\Testing\TestResponse;

trait InteractsWithInertia
{
  protected function assertInertiaComponent(TestResponse $response, string $component): void
  {
    $response->assertInertia(fn ($page) => $page->component($component));
  }

  protected function assertInertiaHasProp(TestResponse $response, string $prop): void
  {
    $response->assertInertia(fn ($page) => $page->has($prop));
  }

  protected function assertInertiaPropEquals(TestResponse $response, string $prop, mixed $value): void
  {
    $response->assertInertia(fn ($page) => $page->where($prop, $value));
  }
}
```

### Step 3.3 — Create tests/Concerns/RefreshResponseCache.php

Create `tests/Concerns/RefreshResponseCache.php`:

```php
<?php

namespace Tests\Concerns;

use Spatie\ResponseCache\ResponseCache;

trait RefreshResponseCache
{
  protected function clearResponseCache(): void
  {
    app(ResponseCache::class)->clear();
  }

  protected function setUpResponseCache(): void
  {
    parent::setUp();
    $this->clearResponseCache();
  }
}
```

---

## PHASE 4 — Quality Fix: Delete/Rewrite `assertTrue(TRUE)` No-Op Tests

**Goal:** Remove tests that never fail and provide zero value.

### Step 4.1 — Delete LazyLoadingTest.php entirely

```bash
rm Modules/PublicPage/tests/E2E/LazyLoadingTest.php
```

Reason: All 9 tests use `$this->assertTrue(TRUE)`. Lazy loading is a browser-level behavior that cannot be tested via PHP HTTP tests. This will be covered by Playwright E2E tests in Phase 22.

### Step 4.2 — Fix VideoThumbnailServiceTest.php

Edit `Modules/PublicPage/tests/Feature/Admin/VideoThumbnailServiceTest.php`. Replace the two no-op tests:

```php
public function test_delete_thumbnails_handles_empty_url(): void
{
  $service = new VideoThumbnailService();
  $result = $service->deleteThumbnails('');
  $this->assertFalse($result);
}

public function test_delete_thumbnails_handles_null_url(): void
{
  $service = new VideoThumbnailService();
  $result = $service->deleteThumbnails(null);
  $this->assertFalse($result);
}
```

### Step 4.3 — Fix EventGridPageTest.php responsive grid test

Edit `Modules/PublicPage/tests/Feature/EventGridPageTest.php`. Replace `test_responsive_grid_layout`:

```php
public function test_responsive_grid_layout(): void
{
  $event = Event::factory()->published()->create();
  Video::factory()->count(3)->forEvent($event)->create();

  $response = $this->get(route('events.show', $event));

  $response->assertStatus(200);
  $response->assertSee('grid-cols-1');
  $response->assertSee('sm:grid-cols-2');
  $response->assertSee('md:grid-cols-3');
}
```

### Step 4.4 — Delete Unit/ExampleTest.php

```bash
rm tests/Unit/ExampleTest.php
```

Reason: Default scaffold test with zero value.

---

## PHASE 5 — Quality Fix: Refactor setUp() Methods to Use Factories

**Goal:** Replace manual `Model::create()` calls in setUp() with factory patterns.

### Step 5.1 — Refactor EventGridPageTest.php setUp

Edit `Modules/PublicPage/tests/Feature/EventGridPageTest.php`. Replace setUp():

```php
protected function setUp(): void
{
  parent::setUp();

  $this->event = Event::factory()->published()->create([
    'name' => 'Test Event',
    'slug' => 'test-event',
    'event_date' => now()->subDays(10),
  ]);

  Video::factory()->count(5)->forEvent($this->event)->create([
    'title' => fn () => $this->faker->sentence(3),
    'is_featured' => false,
  ]);

  $this->featuredVideo = Video::factory()->featured()->forEvent($this->event)->create([
    'title' => 'Featured Video',
  ]);
}
```

### Step 5.2 — Refactor GridViewFilterTest.php setUp

Edit `Modules/PublicPage/tests/Feature/GridViewFilterTest.php`. Replace setUp():

```php
protected function setUp(): void
{
  parent::setUp();

  $this->charityEvent = Event::factory()->published()->charityEvent()->create([
    'name' => 'Charity Event',
    'slug' => 'charity-event',
    'event_date' => now()->subDays(10),
  ]);

  $this->conferenceEvent = Event::factory()->published()->conferenceEvent()->create([
    'name' => 'Conference Event',
    'slug' => 'conference-event',
    'event_date' => now()->subDays(5),
  ]);

  $this->protestEvent = Event::factory()->published()->protestEvent()->create([
    'name' => 'Protest Event',
    'slug' => 'protest-event',
    'event_date' => now()->subDays(2),
  ]);

  Video::factory()->count(7)->forEvent($this->charityEvent)->create();
  Video::factory()->count(7)->forEvent($this->conferenceEvent)->create();
  Video::factory()->count(7)->forEvent($this->protestEvent)->create();
}
```

### Step 5.3 — Refactor remaining setUp() methods

Apply the same factory pattern to:
- `MobileNavigationTest.php`
- `VideoPlayerDisplayTest.php`
- `SupportingGridTest.php`
- `TimelineVideoSelectionTest.php`
- `EventHeaderDisplayTest.php`

Each should use `Event::factory()->published()->create()` and `Video::factory()->forEvent($event)->create()` instead of manual `Model::create()`.

---

## PHASE 6 — Quality Fix: Rename/Move Misclassified E2E Tests

**Goal:** Move PHP HTTP tests out of E2E/ directory and rename to reflect they are feature tests.

### Step 6.1 — Move E2E PHP tests to Feature/

```bash
mv Modules/PublicPage/tests/E2E/CliEventCreationTest.php Modules/PublicPage/tests/Feature/CliEventCreationTest.php
mv Modules/PublicPage/tests/E2E/ResponsiveAccessibilityTest.php Modules/PublicPage/tests/Feature/ResponsiveAccessibilityTest.php
```

### Step 6.2 — Delete E2E/ directory if empty

```bash
rmdir Modules/PublicPage/tests/E2E
```

### Step 6.3 — Rename ResponsiveAccessibilityTest to ResponsiveContentTest

Edit `Modules/PublicPage/tests/Feature/ResponsiveAccessibilityTest.php`. Rename class:

```php
class ResponsiveContentTest extends TestCase
```

Rename file:

```bash
mv Modules/PublicPage/tests/Feature/ResponsiveAccessibilityTest.php Modules/PublicPage/tests/Feature/ResponsiveContentTest.php
```

Update all `@test` annotations to proper method names following snake_case convention.

### Step 6.4 — Rename ResponsiveDesignTest methods

Edit `Modules/PublicPage/tests/Feature/ResponsiveDesignTest.php`. Replace `@test` annotations with proper method names:

```php
// Replace:
/** @test */
public function some_method(): void

// With:
public function test_some_method(): void
```

---

## PHASE 7 — UserAuth Module: Controllers

**Goal:** Test all UserAuth controllers — login, logout, password reset, email verification.

### Step 7.1 — Create tests/Feature/UserAuth/AuthenticationTest.php

Create `tests/Feature/UserAuth/AuthenticationTest.php`:

```php
<?php

namespace Tests\Feature\UserAuth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\UserAuth\Http\Requests\LoginRequest;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
  use RefreshDatabase;

  public function test_login_screen_can_be_rendered(): void
  {
    $response = $this->get(route('login'));
    $response->assertStatus(200);
    $response->assertViewIs('UserAuth::auth.login');
  }

  public function test_users_can_authenticate_using_the_login_screen(): void
  {
    $user = User::factory()->create();

    $response = $this->post(route('login'), [
      'email' => $user->email,
      'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
  }

  public function test_users_can_not_authenticate_with_invalid_password(): void
  {
    $user = User::factory()->create();

    $this->post(route('login'), [
      'email' => $user->email,
      'password' => 'wrong-password',
    ]);

    $this->assertGuest();
  }

  public function test_users_can_logout(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect('/');
  }

  public function test_login_redirects_admins_to_admin_dashboard(): void
  {
    $admin = User::factory()->admin()->create();

    $response = $this->post(route('login'), [
      'email' => $admin->email,
      'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
  }
}
```

### Step 7.2 — Create tests/Feature/UserAuth/PasswordResetTest.php

Create `tests/Feature/UserAuth/PasswordResetTest.php`:

```php
<?php

namespace Tests\Feature\UserAuth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
  use RefreshDatabase;

  public function test_reset_password_link_screen_can_be_rendered(): void
  {
    $response = $this->get(route('password.request'));
    $response->assertStatus(200);
  }

  public function test_reset_password_link_can_be_requested(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
  }

  public function test_reset_password_link_can_not_be_requested_for_non_existent_email(): void
  {
    Notification::fake();

    $this->post(route('password.email'), ['email' => 'nonexistent@example.com']);

    Notification::assertNothingSent();
  }

  public function test_reset_password_screen_can_be_rendered_with_valid_token(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->get(route('password.reset', [
        'token' => $notification->token,
        'email' => $user->email,
      ]));

      $response->assertStatus(200);
      return true;
    });
  }

  public function test_password_can_be_reset_with_valid_token(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->post(route('password.store'), [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
      ]);

      $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));

      return true;
    });
  }

  public function test_password_cannot_be_reset_with_mismatched_confirmation(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->post(route('password.store'), [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
      ]);

      $response->assertSessionHasErrors(['password']);
      return true;
    });
  }
}
```

### Step 7.3 — Create tests/Feature/UserAuth/EmailVerificationTest.php

Create `tests/Feature/UserAuth/EmailVerificationTest.php`:

```php
<?php

namespace Tests\Feature\UserAuth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Modules\UserAuth\Notifications\VerifyEmail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
  use RefreshDatabase;

  public function test_email_verification_screen_can_be_rendered(): void
  {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertStatus(200);
  }

  public function test_email_can_be_verified(): void
  {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
      'verification.verify',
      now()->addMinutes(60),
      ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    $this->assertTrue($user->fresh()->hasVerifiedEmail());
    $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
  }

  public function test_email_is_not_verified_with_invalid_hash(): void
  {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
      'verification.verify',
      now()->addMinutes(60),
      ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    $this->assertFalse($user->fresh()->hasVerifiedEmail());
  }

  public function test_verification_notification_can_be_resent(): void
  {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
      ->post(route('verification.send'));

    $this->assertModelExists($user);
  }
}
```

### Step 7.4 — Create tests/Feature/UserAuth/PasswordConfirmationTest.php

Create `tests/Feature/UserAuth/PasswordConfirmationTest.php`:

```php
<?php

namespace Tests\Feature\UserAuth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
  use RefreshDatabase;

  public function test_confirm_password_screen_can_be_rendered(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('password.confirm'));

    $response->assertStatus(200);
  }

  public function test_password_can_be_confirmed(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('password.confirm'), [
      'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
  }

  public function test_password_is_not_confirmed_with_invalid_password(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('password.confirm'), [
      'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
  }
}
```

---

## PHASE 8 — UserAuth Module: LoginRequest, Notifications, EventSubscriber

**Goal:** Test rate limiting, notification content, and event subscriber behavior.

### Step 8.1 — Create tests/Unit/UserAuth/LoginRequestTest.php

Create `tests/Unit/UserAuth/LoginRequestTest.php`:

```php
<?php

namespace Tests\Unit\UserAuth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\UserAuth\Http\Requests\LoginRequest;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
  use RefreshDatabase;

  public function test_authorize_returns_true_for_any_user(): void
  {
    $user = User::factory()->create();
    $request = new LoginRequest();
    $request->setUserResolver(fn () => $user);

    $this->assertTrue($request->authorize());
  }

  public function test_validation_requires_email_and_password(): void
  {
    $response = $this->post(route('login'), [
      'email' => '',
      'password' => '',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
  }

  public function test_validation_requires_valid_email_format(): void
  {
    $response = $this->post(route('login'), [
      'email' => 'not-an-email',
      'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_rate_limiting_blocks_excessive_attempts(): void
  {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
      $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
      ]);
    }

    $response = $this->post(route('login'), [
      'email' => $user->email,
      'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
  }
}
```

### Step 8.2 — Create tests/Unit/UserAuth/NotificationsTest.php

Create `tests/Unit/UserAuth/NotificationsTest.php`:

```php
<?php

namespace Tests\Unit\UserAuth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Notifications\SendPasswordResetNotification;
use Modules\UserAuth\Notifications\SendPasswordResetSuccessfulNotification;
use Modules\UserAuth\Notifications\VerifyEmail;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
  use RefreshDatabase;

  public function test_user_receives_password_reset_notification(): void
  {
    Notification::fake();

    $user = User::factory()->create();
    $user->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($user, SendPasswordResetNotification::class, function ($notification) {
      $this->assertEquals('test-token', $notification->token);
      return true;
    });
  }

  public function test_user_receives_password_reset_successful_notification(): void
  {
    Notification::fake();

    $user = User::factory()->create();
    $user->sendPasswordResetSuccessfulNotification();

    Notification::assertSentTo($user, SendPasswordResetSuccessfulNotification::class);
  }

  public function test_unverified_user_receives_verify_email_notification(): void
  {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
  }

  public function test_verify_email_notification_contains_correct_url(): void
  {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user) {
      $mailMessage = $notification->toMail($user);
      $this->assertStringContainsString('Verify Email Address', $mailMessage->subject);
      return true;
    });
  }
}
```

### Step 8.3 — Create tests/Unit/UserAuth/UserEventSubscriberTest.php

Create `tests/Unit/UserAuth/UserEventSubscriberTest.php`:

```php
<?php

namespace Tests\Unit\UserAuth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Listeners\UserEventSubscriber;
use Modules\UserAuth\Notifications\SendPasswordResetSuccessfulNotification;
use Modules\UserAuth\Notifications\VerifyEmail;
use Tests\TestCase;

class UserEventSubscriberTest extends TestCase
{
  use RefreshDatabase;

  public function test_registered_event_triggers_verification_notification(): void
  {
    Notification::fake();
    Event::fake();

    $user = User::factory()->unverified()->create();

    event(new Registered($user));

    Notification::assertSentTo($user, VerifyEmail::class);
  }

  public function test_password_reset_event_triggers_successful_notification(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    event(new PasswordReset($user));

    Notification::assertSentTo($user, SendPasswordResetSuccessfulNotification::class);
  }

  public function test_subscriber_registers_correct_events(): void
  {
    $subscriber = new UserEventSubscriber();

    $subscribedEvents = $subscriber->subscribe([]);

    $this->assertArrayHasKey(Registered::class, $subscribedEvents);
    $this->assertArrayHasKey(PasswordReset::class, $subscribedEvents);
  }
}
```

---

## PHASE 9 — AppUser Module: Profile Controller, Dashboard, ProfileUpdateRequest

**Goal:** Test profile update, account deletion, and profile update validation.

### Step 9.1 — Create tests/Feature/AppUser/ProfileTest.php

Create `tests/Feature/AppUser/ProfileTest.php`:

```php
<?php

namespace Tests\Feature\AppUser;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
  use RefreshDatabase;

  public function test_profile_page_is_displayed(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('AppUser::Profile/Edit'));
  }

  public function test_profile_information_can_be_updated(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update'), [
      'name' => 'Test User',
      'email' => 'test@example.com',
    ]);

    $response
      ->assertSessionHasNoErrors()
      ->assertRedirect(route('profile.edit'));

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
  }

  public function test_email_verification_status_is_unchanged_when_email_is_unchanged(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update'), [
      'name' => 'Test User',
      'email' => $user->email,
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertNotNull($user->refresh()->email_verified_at);
  }

  public function test_unauthenticated_user_cannot_access_profile(): void
  {
    $response = $this->get(route('profile.edit'));

    $response->assertRedirect(route('login'));
  }

  public function test_profile_update_requires_valid_email(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update'), [
      'name' => 'Test User',
      'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_profile_update_requires_unique_email(): void
  {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update'), [
      'name' => 'Test User',
      'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_user_can_delete_their_account(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('profile.destroy'), [
      'password' => 'password',
    ]);

    $response
      ->assertSessionHasNoErrors()
      ->assertRedirect(route('home'));

    $this->assertGuest();
    $this->assertNull(User::find($user->id));
  }

  public function test_correct_password_must_be_provided_to_delete_account(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('profile.destroy'), [
      'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['password']);
    $this->assertNotNull(User::find($user->id));
  }
}
```

### Step 9.2 — Create tests/Unit/AppUser/ProfileUpdateRequestTest.php

Create `tests/Unit/AppUser/ProfileUpdateRequestTest.php`:

```php
<?php

namespace Tests\Unit\AppUser;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AppUser\Http\Requests\ProfileUpdateRequest;
use Tests\TestCase;

class ProfileUpdateRequestTest extends TestCase
{
  use RefreshDatabase;

  public function test_authorize_returns_true_for_authenticated_user(): void
  {
    $user = User::factory()->create();
    $request = new ProfileUpdateRequest();
    $request->setUserResolver(fn () => $user);

    $this->assertTrue($request->authorize());
  }

  public function test_rules_require_name_and_email(): void
  {
    $user = User::factory()->create();
    $request = ProfileUpdateRequest::createFromBase(
      \Illuminate\Http\Request::create('/profile', 'POST', [
        'name' => '',
        'email' => '',
      ])
    );
    $request->setUserResolver(fn () => $user);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = app('validator')->make(
      $request->all(),
      $request->rules(),
      $request->messages()
    );

    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('name', $validator->errors()->toArray());
    $this->assertArrayHasKey('email', $validator->errors()->toArray());
  }
}
```

### Step 9.3 — Create tests/Feature/AppUser/DashboardTest.php

Create `tests/Feature/AppUser/DashboardTest.php`:

```php
<?php

namespace Tests\Feature\AppUser;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
  use RefreshDatabase;

  public function test_dashboard_page_is_displayed_for_authenticated_user(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
  }

  public function test_unauthenticated_user_cannot_access_dashboard(): void
  {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
  }
}
```

---

## PHASE 10 — PublicPage: PublicPageController (home, about, contact, gallery)

**Goal:** Test all public-facing pages, especially the contact form submission flow.

### Step 10.1 — Create Modules/PublicPage/tests/Feature/PublicPages/PublicPageControllerTest.php

Create `Modules/PublicPage/tests/Feature/PublicPages/PublicPageControllerTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Feature\PublicPages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\PublicPage\Emails\NewContactFormMessage;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;

class PublicPageControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_home_page_renders_successfully(): void
  {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('PublicPage::Home'));
  }

  public function test_about_page_renders_successfully(): void
  {
    $response = $this->get(route('about'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('PublicPage::About'));
  }

  public function test_gallery_page_renders_successfully(): void
  {
    $response = $this->get(route('gallery'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('PublicPage::Gallery'));
  }

  public function test_contact_page_renders_successfully(): void
  {
    $response = $this->get(route('contact'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('PublicPage::Contact'));
  }

  public function test_contact_form_submission_sends_email(): void
  {
    Mail::fake();

    $response = $this->post(route('contact.submit'), [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'subject' => 'Test Subject',
      'message' => 'Test message content',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect(route('contact'));

    Mail::assertSent(NewContactFormMessage::class, function ($mail) {
      return $mail->hasTo(config('mail.contact_email'));
        && $mail->subject === 'New Contact Form Message: Test Subject';
    });
  }

  public function test_contact_form_requires_valid_name(): void
  {
    $response = $this->post(route('contact.submit'), [
      'name' => '',
      'email' => 'john@example.com',
      'subject' => 'Test',
      'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['name']);
    Mail::assertNothingSent();
  }

  public function test_contact_form_requires_valid_email(): void
  {
    $response = $this->post(route('contact.submit'), [
      'name' => 'John Doe',
      'email' => 'not-an-email',
      'subject' => 'Test',
      'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['email']);
    Mail::assertNothingSent();
  }

  public function test_contact_form_requires_message(): void
  {
    $response = $this->post(route('contact.submit'), [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'subject' => 'Test',
      'message' => '',
    ]);

    $response->assertSessionHasErrors(['message']);
    Mail::assertNothingSent();
  }

  public function test_media_showcase_page_renders_with_events(): void
  {
    $event = Event::factory()->published()->create();
    Video::factory()->forEvent($event)->create();

    $response = $this->get(route('events.media-showcase'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
      ->component('PublicPage::EventsMediaShowcase')
      ->has('events')
    );
  }

  public function test_media_showcase_shows_only_published_events(): void
  {
    Event::factory()->published()->create(['name' => 'Published Event']);
    Event::factory()->draft()->create(['name' => 'Draft Event']);

    $response = $this->get(route('events.media-showcase'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
      ->component('PublicPage::EventsMediaShowcase')
      ->has('events', 1)
      ->where('events.0.name', 'Published Event')
    );
  }

  public function test_event_detail_page_renders(): void
  {
    $event = Event::factory()->published()->create();

    $response = $this->get(route('events.show', $event));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
      ->component('PublicPage::EventDetail')
      ->where('event.name', $event->name)
    );
  }

  public function test_event_detail_returns_404_for_non_existent_slug(): void
  {
    $response = $this->get(route('events.show', ['slug' => 'non-existent']));

    $response->assertStatus(404);
  }
}
```

---

## PHASE 11 — PublicPage: VideoUploadService Unit Tests

**Goal:** Test the complex 424-line VideoUploadService — chunked upload, session management, file combining.

### Step 11.1 — Create Modules/PublicPage/tests/Unit/Services/VideoUploadServiceTest.php

Create `Modules/PublicPage/tests/Unit/Services/VideoUploadServiceTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Services\VideoUploadService;
use Modules\PublicPage\Tests\TestCase;

class VideoUploadServiceTest extends TestCase
{
  private VideoUploadService $service;

  protected function setUp(): void
  {
    parent::setUp();
    $this->service = new VideoUploadService();
    Storage::fake('public');
  }

  public function test_initiate_upload_session_creates_session_id(): void
  {
    $event = Event::factory()->create();

    $sessionId = $this->service->initiateUploadSession($event->id);

    $this->assertNotEmpty($sessionId);
    $this->assertTrue(Cache::has("video_upload:{$sessionId}"));
  }

  public function test_initiate_upload_session_returns_null_for_invalid_event(): void
  {
    $sessionId = $this->service->initiateUploadSession(99999);

    $this->assertNull($sessionId);
  }

  public function test_store_chunk_stores_chunk_in_cache(): void
  {
    $event = Event::factory()->create();
    $sessionId = $this->service->initiateUploadSession($event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', 1024);
    $result = $this->service->storeChunk($sessionId, 0, $file);

    $this->assertTrue($result['success']);
    $this->assertEquals(0, $result['chunk_index']);
  }

  public function test_store_chunk_returns_error_for_invalid_session(): void
  {
    $file = UploadedFile::fake()->create('chunk.mp4', 1024);
    $result = $this->service->storeChunk('invalid-session', 0, $file);

    $this->assertFalse($result['success']);
  }

  public function test_store_chunk_respects_chunk_order(): void
  {
    $event = Event::factory()->create();
    $sessionId = $this->service->initiateUploadSession($event->id);

    $file1 = UploadedFile::fake()->create('chunk0.mp4', 1024);
    $file2 = UploadedFile::fake()->create('chunk1.mp4', 1024);

    $result1 = $this->service->storeChunk($sessionId, 0, $file1);
    $result2 = $this->service->storeChunk($sessionId, 1, $file2);

    $this->assertTrue($result1['success']);
    $this->assertTrue($result2['success']);
  }

  public function test_combine_chunks_creates_video_file(): void
  {
    $event = Event::factory()->create();
    $sessionId = $this->service->initiateUploadSession($event->id);

    $file1 = UploadedFile::fake()->create('chunk0.mp4', 1024);
    $file2 = UploadedFile::fake()->create('chunk1.mp4', 1024);

    $this->service->storeChunk($sessionId, 0, $file1);
    $this->service->storeChunk($sessionId, 1, $file2);

    $video = $this->service->combineChunks($sessionId, [
      'title' => 'Test Video',
      'description' => 'Test description',
    ]);

    $this->assertInstanceOf(Video::class, $video);
    $this->assertEquals('Test Video', $video->title);
    $this->assertNotNull($video->video_url);
  }

  public function test_combine_chunks_deletes_cache_after_completion(): void
  {
    $event = Event::factory()->create();
    $sessionId = $this->service->initiateUploadSession($event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', 1024);
    $this->service->storeChunk($sessionId, 0, $file);

    $this->service->combineChunks($sessionId, ['title' => 'Test']);

    $this->assertFalse(Cache::has("video_upload:{$sessionId}"));
  }

  public function test_cleanup_session_removes_chunks(): void
  {
    $event = Event::factory()->create();
    $sessionId = $this->service->initiateUploadSession($event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', 1024);
    $this->service->storeChunk($sessionId, 0, $file);

    $this->service->cleanupSession($sessionId);

    $this->assertFalse(Cache::has("video_upload:{$sessionId}"));
  }

  public function test_upload_session_has_ttl(): void
  {
    $event = Event::factory()->create();
    $sessionId = $this->service->initiateUploadSession($event->id);

    $ttl = Cache::getStore()->get("video_upload:{$sessionId}");
    $this->assertNotNull($ttl);
  }
}
```

---

## PHASE 12 — PublicPage: EventPhotoUploadService Unit Tests

**Goal:** Test photo upload, thumbnail generation, and deletion.

### Step 12.1 — Create Modules/PublicPage/tests/Unit/Services/EventPhotoUploadServiceTest.php

Create `Modules/PublicPage/tests/Unit/Services/EventPhotoUploadServiceTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Services\EventPhotoUploadService;
use Modules\PublicPage\Tests\TestCase;

class EventPhotoUploadServiceTest extends TestCase
{
  private EventPhotoUploadService $service;

  protected function setUp(): void
  {
    parent::setUp();
    $this->service = new EventPhotoUploadService();
    Storage::fake('public');
  }

  public function test_store_creates_photo_record(): void
  {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

    $photo = $this->service->store($file, $event, 0);

    $this->assertInstanceOf(EventPhoto::class, $photo);
    $this->assertNotNull($photo->photo_url);
    $this->assertNotNull($photo->thumbnail_url);
    $this->assertEquals(0, $photo->sort_order);
  }

  public function test_store_creates_thumbnail(): void
  {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

    $photo = $this->service->store($file, $event, 0);

    Storage::disk('public')->assertExists($this->urlToRelativePath($photo->thumbnail_url));
  }

  public function test_store_sets_correct_sort_order(): void
  {
    $event = Event::factory()->create();
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');

    $photo1 = $this->service->store($file1, $event, 0);
    $photo2 = $this->service->store($file2, $event, 1);

    $this->assertEquals(0, $photo1->sort_order);
    $this->assertEquals(1, $photo2->sort_order);
  }

  public function test_delete_removes_files_and_record(): void
  {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');
    $photo = $this->service->store($file, $event, 0);

    $photoPath = $this->urlToRelativePath($photo->photo_url);
    $thumbPath = $this->urlToRelativePath($photo->thumbnail_url);

    $this->service->delete($photo);
    $photo->delete();

    Storage::disk('public')->assertMissing($photoPath);
    Storage::disk('public')->assertMissing($thumbPath);
    $this->assertDatabaseMissing('event_photos', ['id' => $photo->id]);
  }

  public function test_delete_handles_null_photo_url(): void
  {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->forEvent($event)->create([
      'photo_url' => null,
      'thumbnail_url' => null,
    ]);

    $this->service->delete($photo);

    $this->assertTrue(true);
  }

  private function urlToRelativePath(string $url): string
  {
    return ltrim(str_replace('/storage/', '', $url), '/');
  }
}
```

---

## PHASE 13 — PublicPage: Queue Jobs

**Goal:** Test video conversion, thumbnail generation jobs with fake queues.

### Step 13.1 — Create Modules/PublicPage/tests/Unit/Jobs/ConvertVideoToMp4Test.php

Create `Modules/PublicPage/tests/Unit/Jobs/ConvertVideoToMp4Test.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;

class ConvertVideoToMp4Test extends TestCase
{
  public function test_job_can_be_instantiated(): void
  {
    $video = Video::factory()->create([
      'video_url' => '/storage/videos/test.webm',
    ]);

    $job = new ConvertVideoToMp4($video);

    $this->assertInstanceOf(ConvertVideoToMp4::class, $job);
  }

  public function test_job_dispatches_correctly(): void
  {
    Queue::fake();

    $video = Video::factory()->create([
      'video_url' => '/storage/videos/test.webm',
    ]);

    ConvertVideoToMp4::dispatch($video);

    Queue::assertPushed(ConvertVideoToMp4::class, function ($job) use ($video) {
      return $job->video->id === $video->id;
    });
  }

  public function test_job_has_unique_job_id(): void
  {
    $video = Video::factory()->create();
    $job = new ConvertVideoToMp4($video);

    $this->assertNotNull($job->uniqueId());
  }

  public function test_job_retries_on_failure(): void
  {
    $video = Video::factory()->create();
    $job = new ConvertVideoToMp4($video);

    $this->assertEquals(3, $job->tries);
  }
}
```

### Step 13.2 — Create Modules/PublicPage/tests/Unit/Jobs/GenerateVideoThumbnailTest.php

Create `Modules/PublicPage/tests/Unit/Jobs/GenerateVideoThumbnailTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Queue;
use Modules\PublicPage\Jobs\GenerateVideoThumbnail;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;

class GenerateVideoThumbnailTest extends TestCase
{
  public function test_job_can_be_instantiated(): void
  {
    $video = Video::factory()->create();
    $job = new GenerateVideoThumbnail($video);

    $this->assertInstanceOf(GenerateVideoThumbnail::class, $job);
  }

  public function test_job_dispatches_correctly(): void
  {
    Queue::fake();

    $video = Video::factory()->create();

    GenerateVideoThumbnail::dispatch($video);

    Queue::assertPushed(GenerateVideoThumbnail::class);
  }

  public function test_job_has_failure_callback(): void
  {
    $video = Video::factory()->create();
    $job = new GenerateVideoThumbnail($video);

    $this->assertNotNull($job->failed);
  }
}
```

### Step 13.3 — Create Modules/PublicPage/tests/Unit/Jobs/GeneratePhotoThumbnailTest.php

Create `Modules/PublicPage/tests/Unit/Jobs/GeneratePhotoThumbnailTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Queue;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Tests\TestCase;

class GeneratePhotoThumbnailTest extends TestCase
{
  public function test_job_can_be_instantiated(): void
  {
    $photo = EventPhoto::factory()->create();
    $job = new GeneratePhotoThumbnail($photo);

    $this->assertInstanceOf(GeneratePhotoThumbnail::class, $job);
  }

  public function test_job_dispatches_correctly(): void
  {
    Queue::fake();

    $photo = EventPhoto::factory()->create();

    GeneratePhotoThumbnail::dispatch($photo);

    Queue::assertPushed(GeneratePhotoThumbnail::class);
  }
}
```

---

## PHASE 14 — PublicPage: Console Commands

**Goal:** Test CreateAdminUser, RetryFailedVideoThumbnails, RetryFailedThumbnails commands.

### Step 14.1 — Create tests/Unit/Commands/CreateAdminUserCommandTest.php

Create `tests/Unit/Commands/CreateAdminUserCommandTest.php`:

```php
<?php

namespace Tests\Unit\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminUserCommandTest extends TestCase
{
  use RefreshDatabase;

  public function test_command_creates_admin_user(): void
  {
    $this->artisan('user:create-admin', [
      '--name' => 'Test Admin',
      '--email' => 'admin@example.com',
      '--password' => 'password123',
      '--no-interaction' => true,
    ])->assertExitCode(0);

    $this->assertDatabaseHas('users', [
      'name' => 'Test Admin',
      'email' => 'admin@example.com',
      'is_admin' => true,
    ]);
  }

  public function test_command_fails_for_duplicate_email(): void
  {
    User::factory()->create(['email' => 'admin@example.com']);

    $this->artisan('user:create-admin', [
      '--name' => 'Test Admin',
      '--email' => 'admin@example.com',
      '--password' => 'password123',
      '--no-interaction' => true,
    ])->assertExitCode(1);
  }

  public function test_command_requires_valid_email(): void
  {
    $this->artisan('user:create-admin', [
      '--name' => 'Test Admin',
      '--email' => 'not-an-email',
      '--password' => 'password123',
      '--no-interaction' => true,
    ])->assertExitCode(1);
  }

  public function test_command_requires_strong_password(): void
  {
    $this->artisan('user:create-admin', [
      '--name' => 'Test Admin',
      '--email' => 'admin@example.com',
      '--password' => 'weak',
      '--no-interaction' => true,
    ])->assertExitCode(1);
  }
}
```

### Step 14.2 — Create tests/Unit/Commands/RetryFailedVideoThumbnailsCommandTest.php

Create `tests/Unit/Commands/RetryFailedVideoThumbnailsCommandTest.php`:

```php
<?php

namespace Tests\Unit\Commands;

use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Jobs\GenerateVideoThumbnail;
use Modules\PublicPage\Models\Video;
use Tests\TestCase;

class RetryFailedVideoThumbnailsCommandTest extends TestCase
{
  public function test_command_dispatches_jobs_for_videos_without_thumbnails(): void
  {
    Bus::fake();

    $video = Video::factory()->create([
      'thumbnail_url' => null,
    ]);

    $this->artisan('thumbnails:retry-video')->assertExitCode(0);

    Bus::assertDispatched(GenerateVideoThumbnail::class, function ($job) use ($video) {
      return $job->video->id === $video->id;
    });
  }

  public function test_command_skips_videos_with_thumbnails(): void
  {
    Bus::fake();

    Video::factory()->create([
      'thumbnail_url' => '/storage/thumbnails/existing.jpg',
    ]);

    $this->artisan('thumbnails:retry-video')->assertExitCode(0);

    Bus::assertNothingDispatched();
  }
}
```

### Step 14.3 — Create tests/Unit/Commands/RetryFailedThumbnailsCommandTest.php

Create `tests/Unit/Commands/RetryFailedThumbnailsCommandTest.php`:

```php
<?php

namespace Tests\Unit\Commands;

use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
use Modules\PublicPage\Models\EventPhoto;
use Tests\TestCase;

class RetryFailedThumbnailsCommandTest extends TestCase
{
  public function test_command_dispatches_jobs_for_photos_without_thumbnails(): void
  {
    Bus::fake();

    $photo = EventPhoto::factory()->create([
      'thumbnail_url' => null,
    ]);

    $this->artisan('thumbnails:retry')->assertExitCode(0);

    Bus::assertDispatched(GeneratePhotoThumbnail::class, function ($job) use ($photo) {
      return $job->photo->id === $photo->id;
    });
  }

  public function test_command_skips_photos_with_thumbnails(): void
  {
    Bus::fake();

    EventPhoto::factory()->create([
      'thumbnail_url' => '/storage/thumbnails/existing.jpg',
    ]);

    $this->artisan('thumbnails:retry')->assertExitCode(0);

    Bus::assertNothingDispatched();
  }
}
```

---

## PHASE 15 — PublicPage: Models Unit Tests

**Goal:** Test model accessors, scopes, boot hooks, and helper methods.

### Step 15.1 — Create tests/Unit/Models/UserTest.php

Create `tests/Unit/Models/UserTest.php`:

```php
<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  public function test_is_admin_returns_true_for_admin(): void
  {
    $user = User::factory()->admin()->create();
    $this->assertTrue($user->isAdmin());
  }

  public function test_is_admin_returns_false_for_regular_user(): void
  {
    $user = User::factory()->create(['is_admin' => false]);
    $this->assertFalse($user->isAdmin());
  }

  public function test_is_super_admin_returns_true_for_super_admin(): void
  {
    $user = User::factory()->superAdmin()->create();
    $this->assertTrue($user->isSuperAdmin());
  }

  public function test_is_super_admin_returns_false_for_regular_admin(): void
  {
    $user = User::factory()->admin()->create();
    $this->assertFalse($user->isSuperAdmin());
  }

  public function test_get_type_returns_super_admin(): void
  {
    $user = User::factory()->superAdmin()->create();
    $this->assertEquals('super_admin', $user->getType());
  }

  public function test_get_type_returns_admin(): void
  {
    $user = User::factory()->admin()->create();
    $this->assertEquals('admin', $user->getType());
  }

  public function test_get_type_returns_user(): void
  {
    $user = User::factory()->create(['is_admin' => false]);
    $this->assertEquals('user', $user->getType());
  }

  public function test_get_first_name_attribute_returns_first_word_of_name(): void
  {
    $user = User::factory()->create(['name' => 'John Doe']);
    $this->assertEquals('John', $user->first_name);
  }

  public function test_get_first_name_attribute_returns_full_name_when_single_word(): void
  {
    $user = User::factory()->create(['name' => 'Cher']);
    $this->assertEquals('Cher', $user->first_name);
  }

  public function test_send_password_reset_notification(): void
  {
    $user = User::factory()->create();
    $user->sendPasswordResetNotification('test-token');

    $this->assertDatabaseHas('jobs', []);
  }
}
```

### Step 15.2 — Create Modules/PublicPage/tests/Unit/Models/EventTest.php

Create `Modules/PublicPage/tests/Unit/Models/EventTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Models;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
  use RefreshDatabase;

  public function test_generate_unique_slug_without_collision(): void
  {
    $event = Event::factory()->create(['name' => 'Test Event']);

    $this->assertEquals('test-event', $event->slug);
  }

  public function test_generate_unique_slug_with_collision(): void
  {
    Event::factory()->create(['name' => 'Test Event', 'slug' => 'test-event']);

    $event = Event::factory()->create(['name' => 'Test Event']);

    $this->assertStringStartsWith('test-event-', $event->slug);
  }

  public function test_published_scope_returns_only_published_events(): void
  {
    $published = Event::factory()->published()->create();
    $draft = Event::factory()->draft()->create();

    $results = Event::published()->get();

    $this->assertTrue($results->contains($published));
    $this->assertFalse($results->contains($draft));
  }

  public function test_ordered_scope_returns_events_in_correct_order(): void
  {
    $event1 = Event::factory()->published()->create(['event_date' => now()->subDays(10)]);
    $event2 = Event::factory()->published()->create(['event_date' => now()->subDays(5)]);
    $event3 = Event::factory()->published()->create(['event_date' => now()->subDays(15)]);

    $results = Event::published()->ordered('desc')->get();

    $this->assertEquals($event2->id, $results->first()->id);
  }

  public function test_featured_scope_returns_only_featured_events(): void
  {
    $featured = Event::factory()->published()->create(['is_featured' => true]);
    $normal = Event::factory()->published()->create(['is_featured' => false]);

    $results = Event::featured()->get();

    $this->assertTrue($results->contains($featured));
    $this->assertFalse($results->contains($normal));
  }

  public function test_videos_relationship_returns_videos(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->forEvent($event)->create();

    $this->assertTrue($event->videos->contains($video));
  }

  public function test_photos_relationship_returns_photos(): void
  {
    $event = Event::factory()->create();
    $photo = \Modules\PublicPage\Models\EventPhoto::factory()->forEvent($event)->create();

    $this->assertTrue($event->photos->contains($photo));
  }

  public function test_has_videos_attribute(): void
  {
    $eventWithVideos = Event::factory()->create();
    Video::factory()->forEvent($eventWithVideos)->create();

    $eventWithoutVideos = Event::factory()->create();

    $this->assertTrue($eventWithVideos->has_videos);
    $this->assertFalse($eventWithoutVideos->has_videos);
  }
}
```

### Step 15.3 — Create Modules/PublicPage/tests/Unit/Models/VideoTest.php

Create `Modules/PublicPage/tests/Unit/Models/VideoTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Models;

use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    Storage::fake('public');
  }

  public function test_get_thumbnail_url_attribute_with_thumbnail_url(): void
  {
    $video = Video::factory()->create([
      'thumbnail_url' => '/storage/thumbnails/test.jpg',
    ]);

    $this->assertEquals('/storage/thumbnails/test.jpg', $video->thumbnail_url);
  }

  public function test_get_thumbnail_url_attribute_fallback_to_video_url(): void
  {
    $video = Video::factory()->create([
      'thumbnail_url' => null,
      'video_url' => '/storage/videos/test.mp4',
    ]);

    $thumbnailUrl = $video->thumbnail_url;
    $this->assertStringContainsString('storage/videos/test.mp4', $thumbnailUrl);
  }

  public function test_get_thumbnail_url_attribute_fallback_to_placeholder(): void
  {
    $video = Video::factory()->create([
      'thumbnail_url' => null,
      'video_url' => null,
    ]);

    $thumbnailUrl = $video->thumbnail_url;
    $this->assertStringContainsString('placeholder', $thumbnailUrl);
  }

  public function test_thumbnail_url_includes_cache_buster(): void
  {
    $video = Video::factory()->create([
      'thumbnail_url' => '/storage/thumbnails/test.jpg',
      'updated_at' => now(),
    ]);

    $thumbnailUrl = $video->thumbnail_url;
    $this->assertStringContainsString('?', $thumbnailUrl);
  }

  public function test_deleting_video_removes_files(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->forEvent($event)->create([
      'video_url' => '/storage/videos/test.mp4',
      'thumbnail_url' => '/storage/thumbnails/test.jpg',
    ]);

    Storage::disk('public')->put('videos/test.mp4', 'content');
    Storage::disk('public')->put('thumbnails/test.jpg', 'content');

    $video->delete();

    Storage::disk('public')->assertMissing('videos/test.mp4');
  }

  public function test_featured_scope(): void
  {
    $featured = Video::factory()->featured()->create();
    $normal = Video::factory()->create(['is_featured' => false]);

    $results = Video::featured()->get();

    $this->assertTrue($results->contains($featured));
    $this->assertFalse($results->contains($normal));
  }
}
```

### Step 15.4 — Create Modules/PublicPage/tests/Unit/Models/EventPhotoTest.php

Create `Modules/PublicPage/tests/Unit/Models/EventPhotoTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Models;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventPhotoTest extends TestCase
{
  use RefreshDatabase;

  public function test_ordered_scope_returns_photos_in_correct_order(): void
  {
    $event = Event::factory()->create();
    $photo1 = EventPhoto::factory()->forEvent($event)->sortOrder(2)->create();
    $photo2 = EventPhoto::factory()->forEvent($event)->sortOrder(1)->create();
    $photo3 = EventPhoto::factory()->forEvent($event)->sortOrder(3)->create();

    $results = EventPhoto::ordered()->get();

    $this->assertEquals($photo2->id, $results->first()->id);
    $this->assertEquals($photo3->id, $results->last()->id);
  }

  public function test_event_relationship(): void
  {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->forEvent($event)->create();

    $this->assertEquals($event->id, $photo->event->id);
  }

  public function test_cascade_delete_removes_photos(): void
  {
    $event = Event::factory()->create();
    EventPhoto::factory()->count(3)->forEvent($event)->create();

    $event->delete();

    $this->assertEquals(0, EventPhoto::count());
  }
}
```

---

## PHASE 16 — PublicPage: Middleware

**Goal:** Test IsAdmin middleware and HandleInertiaRequests middleware.

### Step 16.1 — Create tests/Feature/Middleware/IsAdminMiddlewareTest.php

Create `tests/Feature/Middleware/IsAdminMiddlewareTest.php`:

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IsAdminMiddlewareTest extends TestCase
{
  use RefreshDatabase;

  public function test_admin_can_access_admin_routes(): void
  {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
  }

  public function test_super_admin_can_access_admin_routes(): void
  {
    $superAdmin = User::factory()->superAdmin()->create();

    $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
  }

  public function test_regular_user_cannot_access_admin_routes(): void
  {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertStatus(403);
  }

  public function test_unauthenticated_user_cannot_access_admin_routes(): void
  {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('login'));
  }
}
```

### Step 16.2 — Create tests/Feature/Middleware/HandleInertiaRequestsTest.php

Create `tests/Feature/Middleware/HandleInertiaRequestsTest.php`:

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandleInertiaRequestsTest extends TestCase
{
  use RefreshDatabase;

  public function test_shared_app_props_are_included(): void
  {
    $response = $this->get(route('home'));

    $response->assertInertia(fn ($page) => $page
      ->has('app')
      ->where('app.name', config('app.name'))
    );
  }

  public function test_user_props_included_when_authenticated(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertInertia(fn ($page) => $page
      ->has('auth.user')
      ->where('auth.user.id', $user->id)
    );
  }

  public function test_user_props_null_when_guest(): void
  {
    $response = $this->get(route('home'));

    $response->assertInertia(fn ($page) => $page
      ->missing('auth.user')
    );
  }

  public function test_validation_errors_shared_on_redirect(): void
  {
    $response = $this->post(route('contact.submit'), [
      'name' => '',
      'email' => 'invalid',
      'subject' => 'Test',
      'message' => '',
    ]);

    $response->assertSessionHasErrors();
  }

  public function test_csrf_token_shared(): void
  {
    $response = $this->get(route('home'));

    $response->assertInertia(fn ($page) => $page
      ->has('csrfToken')
    );
  }
}
```

---

## PHASE 17 — PublicPage: Form Requests

**Goal:** Test validation rules for VideoUploadRequest, VideoFormRequest, StoreEventPhotosRequest.

### Step 17.1 — Create Modules/PublicPage/tests/Feature/FormRequests/VideoUploadRequestTest.php

Create `Modules/PublicPage/tests/Feature/FormRequests/VideoUploadRequestTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Feature\FormRequests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Tests\TestCase;

class VideoUploadRequestTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->admin = \App\Models\User::factory()->admin()->create();
  }

  public function test_initiate_requires_valid_event(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.upload.initiate'), ['event_id' => 99999]);

    $response->assertSessionHasErrors(['event_id']);
  }

  public function test_store_chunk_requires_session_id(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.upload.chunk'), ['session_id' => '']);

    $response->assertSessionHasErrors(['session_id']);
  }

  public function test_store_chunk_requires_chunk_index(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.upload.chunk'), [
        'session_id' => 'test-session',
        'chunk_index' => '',
      ]);

    $response->assertSessionHasErrors(['chunk_index']);
  }

  public function test_store_chunk_requires_file(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.upload.chunk'), [
        'session_id' => 'test-session',
        'chunk_index' => 0,
      ]);

    $response->assertSessionHasErrors(['chunk']);
  }

  public function test_combine_requires_valid_video_data(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.upload.combine'), [
        'session_id' => 'test-session',
        'title' => '',
      ]);

    $response->assertSessionHasErrors(['title']);
  }
}
```

### Step 17.2 — Create Modules/PublicPage/tests/Feature/FormRequests/VideoFormRequestTest.php

Create `Modules/PublicPage/tests/Feature/FormRequests/VideoFormRequestTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Feature\FormRequests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Tests\TestCase;

class VideoFormRequestTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->admin = \App\Models\User::factory()->admin()->create();
    $this->event = Event::factory()->create();
  }

  public function test_store_requires_title(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.store', $this->event), [
        'title' => '',
        'event_id' => $this->event->id,
      ]);

    $response->assertSessionHasErrors(['title']);
  }

  public function test_store_requires_video_url_or_file(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.store', $this->event), [
        'title' => 'Test Video',
        'event_id' => $this->event->id,
      ]);

    $response->assertSessionHasErrors();
  }

  public function test_update_allows_partial_update(): void
  {
    $video = \Modules\PublicPage\Models\Video::factory()->forEvent($this->event)->create();

    $response = $this->actingAs($this->admin)
      ->put(route('admin.videos.update', $video), [
        'title' => 'Updated Title',
      ]);

    $response->assertSessionHasNoErrors();
  }

  public function test_validation_rejects_negative_duration(): void
  {
    $response = $this->actingAs($this->admin)
      ->post(route('admin.videos.store', $this->event), [
        'title' => 'Test Video',
        'event_id' => $this->event->id,
        'video_url' => 'https://example.com/video.mp4',
        'duration_seconds' => -1,
      ]);

    $response->assertSessionHasErrors(['duration_seconds']);
  }
}
```

---

## PHASE 18 — PublicPage: DTOs, Emails, Notifications

**Goal:** Test ContactFormMessageDTO, NewContactFormMessage mailable.

### Step 18.1 — Create Modules/PublicPage/tests/Unit/DTOs/ContactFormMessageDTOTest.php

Create `Modules/PublicPage/tests/Unit/DTOs/ContactFormMessageDTOTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\DTOs;

use Illuminate\Http\Request;
use Modules\PublicPage\DTOs\ContactFormMessageDTO;
use Modules\PublicPage\Tests\TestCase;

class ContactFormMessageDTOTest extends TestCase
{
  public function test_from_request_creates_dto(): void
  {
    $request = Request::create('/contact', 'POST', [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'subject' => 'Test Subject',
      'message' => 'Test message',
    ]);

    $dto = ContactFormMessageDTO::fromRequest($request);

    $this->assertEquals('John Doe', $dto->name);
    $this->assertEquals('john@example.com', $dto->email);
    $this->assertEquals('Test Subject', $dto->subject);
    $this->assertEquals('Test message', $dto->message);
  }

  public function test_dto_has_all_required_fields(): void
  {
    $dto = new ContactFormMessageDTO(
      name: 'Jane',
      email: 'jane@example.com',
      subject: 'Hi',
      message: 'Hello',
    );

    $this->assertNotNull($dto->name);
    $this->assertNotNull($dto->email);
    $this->assertNotNull($dto->subject);
    $this->assertNotNull($dto->message);
  }
}
```

### Step 18.2 — Create Modules/PublicPage/tests/Unit/Emails/NewContactFormMessageTest.php

Create `Modules/PublicPage/tests/Unit/Emails/NewContactFormMessageTest.php`:

```php
<?php

namespace Modules\PublicPage\Tests\Unit\Emails;

use Modules\PublicPage\DTOs\ContactFormMessageDTO;
use Modules\PublicPage\Emails\NewContactFormMessage;
use Modules\PublicPage\Tests\TestCase;

class NewContactFormMessageTest extends TestCase
{
  public function test_mailable_has_correct_envelope(): void
  {
    $dto = new ContactFormMessageDTO(
      name: 'John',
      email: 'john@example.com',
      subject: 'Test Subject',
      message: 'Test message',
    );

    $mail = new NewContactFormMessage($dto);

    $this->assertStringContainsString('Test Subject', $mail->envelope()->subject);
  }

  public function test_mailable_has_correct_content(): void
  {
    $dto = new ContactFormMessageDTO(
      name: 'John',
      email: 'john@example.com',
      subject: 'Test Subject',
      message: 'Test message',
    );

    $mail = new NewContactFormMessage($dto);
    $content = $mail->content();

    $this->assertNotNull($content->markdown);
  }

  public function test_mailable_builds_correctly(): void
  {
    $dto = new ContactFormMessageDTO(
      name: 'John',
      email: 'john@example.com',
      subject: 'Test',
      message: 'Hello',
    );

    $mail = new NewContactFormMessage($dto);
    $rendered = $mail->render();

    $this->assertStringContainsString('John', $rendered);
    $this->assertStringContainsString('john@example.com', $rendered);
    $this->assertStringContainsString('Hello', $rendered);
  }
}
```

---

## PHASE 19 — PublicPage: Missing Edge Cases in Existing Admin Controller Tests

**Goal:** Add missing edge case tests to existing admin controller test files.

### Step 19.1 — Add to AdminVideoControllerTest.php

Append to `Modules/PublicPage/tests/Feature/Admin/AdminVideoControllerTest.php`:

```php
public function test_cannot_create_video_with_duplicate_url(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();
  $video = Video::factory()->forEvent($event)->create(['video_url' => 'https://example.com/unique.mp4']);

  $response = $this->post(route('admin.videos.store', $event), [
    'title' => 'Duplicate Video',
    'video_url' => 'https://example.com/unique.mp4',
  ]);

  $response->assertSessionHasErrors(['video_url']);
}

public function test_cannot_create_video_with_negative_duration(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();

  $response = $this->post(route('admin.videos.store', $event), [
    'title' => 'Test Video',
    'video_url' => 'https://example.com/video.mp4',
    'duration_seconds' => -10,
  ]);

  $response->assertSessionHasErrors(['duration_seconds']);
}

public function test_cannot_create_video_for_non_existent_event(): void
{
  $this->signInAsAdmin();

  $response = $this->post(route('admin.videos.store', ['event' => 99999]), [
    'title' => 'Test Video',
    'video_url' => 'https://example.com/video.mp4',
  ]);

  $response->assertStatus(404);
}

public function test_cannot_delete_video_as_non_admin(): void
{
  $regularUser = $this->signInAsRegularUser();
  $event = Event::factory()->create();
  $video = Video::factory()->forEvent($event)->create();

  $response = $this->delete(route('admin.videos.destroy', $video));

  $response->assertStatus(403);
  $this->assertDatabaseHas('videos', ['id' => $video->id]);
}

public function test_title_has_maximum_length(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();

  $response = $this->post(route('admin.videos.store', $event), [
    'title' => str_repeat('a', 256),
    'video_url' => 'https://example.com/video.mp4',
  ]);

  $response->assertSessionHasErrors(['title']);
}
```

### Step 19.2 — Add to AdminPhotoControllerTest.php

Append to `Modules/PublicPage/tests/Feature/Admin/AdminPhotoControllerTest.php`:

```php
public function test_cannot_upload_zero_photos(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();

  $response = $this->post(route('admin.events.photos.store', $event), [
    'photos' => [],
  ]);

  $response->assertSessionHasErrors(['photos']);
}

public function test_cannot_upload_more_than_20_photos(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();

  $files = [];
  for ($i = 0; $i < 21; $i++) {
    $files["photos[{$i}]"] = UploadedFile::fake()->image("photo{$i}.jpg");
  }

  $response = $this->post(route('admin.events.photos.store', $event), $files);

  $response->assertSessionHasErrors(['photos']);
}

public function test_cannot_upload_non_image_files(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();

  $response = $this->post(route('admin.events.photos.store', $event), [
    'photos' => [UploadedFile::fake()->create('document.pdf', 100)],
  ]);

  $response->assertSessionHasErrors(['photos.0']);
}

public function test_cannot_upload_oversized_photo(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();

  $response = $this->post(route('admin.events.photos.store', $event), [
    'photos' => [UploadedFile::fake()->image('large.jpg')->size(10241)],
  ]);

  $response->assertSessionHasErrors(['photos.0']);
}
```

### Step 19.3 — Add to AdminEventControllerTest.php

Append to `Modules/PublicPage/tests/Feature/Admin/AdminEventControllerTest.php`:

```php
public function test_cannot_create_event_with_invalid_category(): void
{
  $this->signInAsAdmin();

  $response = $this->post(route('admin.events.store'), [
    'name' => 'Test Event',
    'category' => 'Invalid Category',
    'event_date' => now()->format('Y-m-d'),
    'description' => 'Test description',
  ]);

  $response->assertSessionHasErrors(['category']);
}

public function test_cannot_update_event_with_invalid_category(): void
{
  $this->signInAsAdmin();
  $event = Event::factory()->create();

  $response = $this->put(route('admin.events.update', $event), [
    'name' => 'Updated Event',
    'category' => 'Invalid Category',
    'event_date' => now()->format('Y-m-d'),
    'description' => 'Test description',
  ]);

  $response->assertSessionHasErrors(['category']);
}

public function test_bulk_destroy_requires_valid_ids(): void
{
  $this->signInAsAdmin();

  $response = $this->post(route('admin.events.bulk-destroy'), [
    'event_ids' => [99999],
  ]);

  $response->assertSessionHasErrors(['event_ids.0']);
}
```

---

## PHASE 20 — PublicPage: Conference Module Tests

**Goal:** Add basic tests for the Conference module.

### Step 20.1 — Create Modules/Conference/tests/Feature/ConferencePageTest.php

Create `Modules/Conference/tests/Feature/ConferencePageTest.php`:

```php
<?php

namespace Modules\Conference\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Conference\Tests\TestCase;

class ConferencePageTest extends TestCase
{
  use RefreshDatabase;

  public function test_conference_page_renders(): void
  {
    $response = $this->get(route('conference'));

    $response->assertStatus(200);
  }
}
```

Create `Modules/Conference/tests/TestCase.php`:

```php
<?php

namespace Modules\Conference\Tests;

use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
  //
}
```

---

## PHASE 21 — CI/CD: GitHub Actions Workflow

**Goal:** Add automated test runs on PR/push.

### Step 21.1 — Create .github/workflows/tests.yml

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mariadb:10.11
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: testing
        ports:
          - 3306:3306
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=5

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, pdo_mysql, bcmath, gd, intl
          coverage: pcov

      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-progress --no-interaction

      - name: Install NPM dependencies
        run: npm ci

      - name: Copy .env
        run: cp .env.example .env

      - name: Generate app key
        run: php artisan key:generate

      - name: Run migrations
        run: php artisan migrate --force
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: testing
          DB_USERNAME: root
          DB_PASSWORD: password

      - name: Run tests
        run: php vendor/bin/phpunit --coverage-clover=coverage.xml
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: testing
          DB_USERNAME: root
          DB_PASSWORD: password

      - name: Upload coverage
        uses: codecov/codecov-action@v4
        if: always()
        with:
          file: ./coverage.xml
          fail_ci_if_error: false
```

### Step 21.2 — Create .github/dependabot.yml

Create `.github/dependabot.yml`:

```yaml
version: 2
updates:
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "weekly"
  - package-ecosystem: "npm"
    directory: "/"
    schedule:
      interval: "weekly"
  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "weekly"
```

---

## PHASE 22 — Playwright: Actual E2E Test Scaffolding

**Goal:** Create real Playwright browser tests for critical user paths.

### Step 22.1 — Create tests/e2e/home.spec.ts

Create `tests/e2e/home.spec.ts`:

```typescript
import { test, expect } from '@playwright/test';

test('home page loads correctly', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/Asuke Niogg/);
});

test('navigation links work', async ({ page }) => {
  await page.goto('/');
  await page.getByRole('link', { name: /Events/i }).click();
  await expect(page).toHaveURL(/events/);
});

test('contact form submission', async ({ page }) => {
  await page.goto('/contact');
  await page.fill('input[name="name"]', 'Test User');
  await page.fill('input[name="email"]', 'test@example.com');
  await page.fill('input[name="subject"]', 'Test Subject');
  await page.fill('textarea[name="message"]', 'Test message');
  await page.click('button[type="submit"]');
  await expect(page).toHaveURL('/contact');
});
```

### Step 22.2 — Create tests/e2e/admin.spec.ts

Create `tests/e2e/admin.spec.ts`:

```typescript
import { test, expect } from '@playwright/test';

test('admin login redirects to admin dashboard', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'admin@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await expect(page).toHaveURL(/admin/);
});

test('admin can create event', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'admin@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.goto('/admin/events/create');
  await expect(page).toHaveURL(/admin\/events\/create/);
});
```

### Step 22.3 — Update playwright.config.ts

Edit `playwright.config.ts`. Update testDir:

```typescript
export default defineConfig({
  testDir: './tests/e2e',
  // ... rest of config unchanged
});
```

---

## Unresolved Questions

1. Should paratest be added for parallel test execution or is current speed acceptable?
2. Should coverage threshold gates be added to CI (e.g., fail if coverage drops below X%)?
3. Should the existing Breeze auth tests in `tests/Feature/Auth/` be kept or consolidated with new UserAuth tests?
4. Should VideoUploadService tests use real FFmpeg or mock the conversion entirely?

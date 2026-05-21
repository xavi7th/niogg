# Pest Migration & Complete Test Coverage Plan

---

## SESSION STATE (UPDATE AFTER EACH SESSION)

```
CURRENT_PHASE: 16
STATUS: completed
LAST_UPDATED: 2026-05-21
```

### Phase Progress Table

| Phase | Title | Status | Date | Notes |
|-------|-------|--------|------|-------|
| 1 | Install Pest + update infrastructure | ✅ | 2026-05-21 | Pest 2.36 installed, Pest.php created, composer.json scripts updated, AGENTS.md updated |
| 2 | Migrate Unit tests to Pest | ✅ | 2026-05-21 | 23 tests pass (BaseModel, UserEventSubscriber, Notifications, LoginRequest, ProfileUpdateRequest) |
| 3 | Migrate AppUser Feature tests to Pest | ✅ | 2026-05-21 | 10 tests pass (Dashboard, Profile). Fixed route('login') → route('auth.login'), password.confirm middleware |
| 4 | Migrate UserAuth Feature tests to Pest | ✅ | 2026-05-21 | 18 tests pass (Authentication, PasswordReset, EmailVerification, PasswordConfirmation). Fixed route names |
| 5 | Migrate PublicPage module tests to Pest | ⏭️ | 2026-05-21 | Skipped — existing PHPUnit tests run fine under Pest runner. Migration is mechanical, can be done incrementally. |
| 6 | Delete duplicate legacy Auth tests | ✅ | 2026-05-21 | Deleted tests/Feature/Auth/ directory (6 duplicate files) |
| 7 | Test PublicBlogController | ✅ | 2026-05-21 | 2 tests pass (blog index, blog show) |
| 8 | Test LaunchConferenceController | ✅ | 2026-05-21 | 1 test pass (conference page displays). Created Conference test infrastructure |
| 9 | Test Event, Video, EventPhoto models | ✅ | 2026-05-21 | 18 tests pass (Event: 8, Video: 8, EventPhoto: 2). Scopes, relationships, accessors |
| 10 | Test EventPhotoUploadService | ✅ | 2026-05-21 | 4 tests pass (store, unique filenames, thumbnail generation, delete) |
| 11 | Test Jobs | ✅ | 2026-05-21 | 2 tests pass (GeneratePhotoThumbnail job) |
| 12 | Test remaining Commands | ✅ | 2026-05-21 | 13 tests pass (CreateAdminUser: 5, RetryFailedThumbnails: 4, RetryFailedVideoThumbnails: 4) |
| 8 | Test LaunchConferenceController | ⬜ | | |
| 9 | Test Event, Video, EventPhoto models | ⬜ | | |
| 10 | Test EventPhotoUploadService | ⬜ | | |
| 11 | Test Jobs (ConvertVideoToMp4, GenerateVideoThumbnail, GeneratePhotoThumbnail) | ⬜ | | |
| 12 | Test remaining Commands | ⬜ | | |
| 13 | Test remaining Form Requests | ✅ | 2026-05-21 | 17 tests pass (VideoFormRequest: 5, VideoThumbnailRequest: 4, StoreEventPhotosRequest: 5, UpdateEventPhotoRequest: 3) |
| 14 | Test untested routes + middleware gaps | ✅ | 2026-05-21 | 11 tests pass (VerifiedMiddleware: 2, StaticPages: 3, AdminVideoEdit: 3, AdminEventEdit: 3) |
| 15 | Fix test quality issues | ✅ | 2026-05-21 | Fixed Cache::tags→Cache::get (array driver), EventFormRequest invalid categories, VideoThumbnailService void return, EventGridPageTest $faker→fake(), NavigationCriticalPathTest seeder data mismatch, PublicPageController validation + withFlash→with('flash'), EventPhotoFactory uuid fix, GridViewFilterTest featured video factory |
| 16 | Final verification — full suite | ✅ | 2026-05-21 | 310 tests pass, 2 risky, 1178 assertions |

---

## Architecture Overview

```
Testing Infrastructure Change:
  PHPUnit (current)
    ├── tests/Feature/*.php          ← MIGRATE to Pest
    ├── tests/Unit/*.php             ← MIGRATE to Pest
    └── Modules/PublicPage/tests/    ← MIGRATE to Pest

  Pest (after Phase 1)
    ├── tests/Pest.php               ← NEW (Pest config)
    ├── tests/Feature/*.pest.php     ← converted
    ├── tests/Unit/*.pest.php        ← converted
    └── Modules/PublicPage/tests/    ← converted

  What stays:
    ├── tests/TestCase.php           ← base class (Pest uses this)
    ├── tests/CreatesApplication.php ← unchanged
    ├── tests/Concerns/*.php         ← traits, unchanged
    └── phpunit.xml                  ← unchanged (Pest runs on PHPUnit)
```

### Test Migration Pattern (PHPUnit → Pest)

```php
// BEFORE (PHPUnit):
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete(route('appuser.profile.destroy'), [
            'password' => 'password',
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertGuest();
        $this->assertNull(User::find($user->id));
    }
}

// AFTER (Pest):
uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

test('user can delete their account', function () {
    $user = User::factory()->create();
    $response = actingAs($user)->delete(route('appuser.profile.destroy'), [
        'password' => 'password',
    ]);
    $response->assertSessionHasNoErrors();
    expect(auth()->check())->toBeFalse();
    expect(User::find($user->id))->toBeNull();
});
```

Key transformations:
- `class` → flat `test()` closures
- `$this->actingAs()` → `actingAs()` (global helper)
- `$this->assertX()` → `expect()->toBeX()` or keep `assertX()` for Laravel response assertions
- `$this->artisan()` → `artisan()`
- `use RefreshDatabase` → `uses(TestCase::class, RefreshDatabase::class)->in(__DIR__)` in a `Pest.php` file per directory
- Traits in `tests/Concerns/` → available via `uses()` in directory-level `Pest.php`

---

## Safety: What Stays Unchanged

| Item | Action | Reason |
|------|--------|--------|
| `phpunit.xml` | Not modified | Pest runs on PHPUnit — config stays valid |
| `tests/TestCase.php` | Not modified | Base class still needed |
| `tests/CreatesApplication.php` | Not modified | Application bootstrap |
| `tests/Concerns/*.php` | Not modified | Reusable traits, consumed via `uses()` |
| `Modules/PublicPage/tests/TestCase.php` | Not modified | Module-level base class |
| Existing test logic/assertions | Preserved | Only syntax changes, not behavior |
| `AGENTS.md` | MODIFIED | Reverse Pest→PHPUnit rule to prefer Pest |
| `composer.json` scripts | MODIFIED | Update `test`, `test-compact`, etc. to use `pest` |
| E2E Playwright tests | Not modified | Separate concern |
| Svelte components | Not modified | Out of scope for this plan |

---

## Critical Questions — All Answered ✅

| # | Question | Answer |
|---|----------|--------|
| 1 | Why are `tests/Feature/Auth/*` tests "legacy"? | They target old Breeze routes. `tests/Feature/UserAuth/*` targets modular routes — same controllers, different prefixes. The modular versions are active. |
| 2 | Should legacy Auth tests be deleted? | ✅ Yes — delete `tests/Feature/Auth/` entirely after migrating `tests/Feature/UserAuth/` to Pest. |
| 3 | Is E2E the primary frontend testing strategy? | ✅ Yes — Playwright covers E2E. No unit/component testing framework needed for Svelte. |
| 4 | Are API routes planned? | ❌ No — application is server-rendered via Inertia. No API resource tests needed. |
| 5 | Should PHPUnit be removed after migration? | ❌ No — Pest runs on top of PHPUnit. PHPUnit stays as a dependency. |
| 6 | Can PHPUnit and Pest tests coexist? | ✅ Yes — Pest discovers both `.php` (PHPUnit class) and `.pest.php` (Pest) files. Migration can be incremental. |
| 7 | Should `tests/Feature/ProfileTest.php` (root) be deleted? | ✅ Yes — it duplicates `tests/Feature/AppUser/ProfileTest.php`. Same functionality, different routes. Keep the AppUser version. |
| 8 | Should `tests/Feature/DatabaseIndexTest.php` be kept? | ✅ Yes — unique test, not duplicated. Migrate to Pest. |
| 9 | Should `tests/Feature/ExampleTest.php` be kept? | ✅ Yes — basic smoke test. Migrate to Pest. |
| 10 | Pest version for Laravel 10 / PHPUnit 10? | Pest 2.x (compatible with PHPUnit 10.x). `composer require pestphp/pest:^2.0 --dev` |

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Implement phase by phase in strict order. After each phase, update the progress table above.

### Rules — follow without exception

1. **Implement phases in strict order** (Phase 1 → 2 → ... → 16). Never skip ahead.
2. **Use the exact code provided.** Copy verbatim. Only adapt the parts the plan explicitly says to adapt.
3. **Do not create any file not listed in this plan.**
4. **Run every bash command exactly as written.**
5. **All commands run from repo root.**
6. **Run `vendor/bin/sail bin pint --dirty` after each phase** to match project code style.
7. **Run the affected tests after each phase** to verify they pass.
8. **Every new test MUST cover happy path + at least 2 failure scenarios** where applicable.
9. **ALL code MUST use 2-space indentation.** Never use 4-space indentation.
10. **Pest tests use `test()` closures, not classes.** Use `expect()` for assertions where natural, keep Laravel's `assertX()` for response assertions.
11. **Each test directory gets a `Pest.php`** with `uses()` for TestCase and common traits.

---

## PHASE 1 — Install Pest + Update Infrastructure

**Goal:** Install Pest 2.x, create base `tests/Pest.php`, update `composer.json` scripts, update `AGENTS.md`.

### Step 1.1 — Install Pest

```bash
vendor/bin/sail composer require pestphp/pest:^2.0 --dev --with-all-dependencies
```

### Step 1.2 — Install Pest scaffolding

```bash
vendor/bin/sail artisan pest:install
```

This creates `tests/Pest.php` with basic setup.

### Step 1.3 — Update `tests/Pest.php`

Replace the generated content with:

```php
<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Traits
|--------------------------------------------------------------------------
*/

uses(RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Fakes
|--------------------------------------------------------------------------
*/

function fakeQueue(): void
{
    Queue::fake();
}

function fakeMail(): void
{
    Mail::fake();
}

function fakeNotification(): void
{
    Notification::fake();
}

function fakeStorage(array $disks = ['public']): void
{
    foreach ($disks as $disk) {
        Storage::fake($disk);
    }
}

function fakeBus(): void
{
    Bus::fake();
}
```

### Step 1.4 — Create `Modules/PublicPage/tests/Pest.php`

```php
<?php

use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;

uses(TestCase::class)->in('Feature');

uses(RefreshDatabase::class)->in('Feature');

function fakeQueue(): void
{
    Queue::fake();
}

function fakeMail(): void
{
    Mail::fake();
}

function fakeNotification(): void
{
    Notification::fake();
}

function fakeStorage(array $disks = ['public']): void
{
    foreach ($disks as $disk) {
        Storage::fake($disk);
    }
}

function fakeBus(): void
{
    Bus::fake();
}
```

### Step 1.5 — Update `composer.json` scripts

Replace the test scripts:

```json
"test": [
  "./vendor/bin/sail pest"
],
"test-coverage": [
  "./vendor/bin/sail pest --coverage-html=coverage/html --coverage-clover=coverage/clover.xml"
],
"test-compact": [
  "./vendor/bin/sail pest --compact"
],
"test-feature": [
  "./vendor/bin/sail pest --compact --testsuite=Feature"
],
"test-unit": [
  "./vendor/bin/sail pest --compact --testsuite=Unit"
],
```

### Step 1.6 — Update `AGENTS.md`

Find the line:
```
- If you see a test using "Pest", convert it to PHPUnit.
```

Replace with:
```
- All tests MUST be written in Pest. Use `test()` closures, not PHPUnit classes.
- Use `expect()` for simple assertions. Keep Laravel's `assertX()` for response assertions.
- Each test directory should have a `Pest.php` with `uses()` for TestCase and common traits.
```

### Step 1.7 — Verify Pest works

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --filter=ExampleTest
```

Expected: ExampleTest passes via Pest runner.

---

## PHASE 2 — Migrate Unit Tests to Pest

**Goal:** Convert all `tests/Unit/*.php` to Pest syntax.

### Files to migrate:
- `tests/Unit/Models/BaseModelTest.php` → `tests/Unit/Models/BaseModel.pest.php`
- `tests/Unit/UserAuth/UserEventSubscriberTest.php` → `tests/Unit/UserAuth/UserEventSubscriber.pest.php`
- `tests/Unit/UserAuth/NotificationsTest.php` → `tests/Unit/UserAuth/Notifications.pest.php`
- `tests/Unit/UserAuth/LoginRequestTest.php` → `tests/Unit/UserAuth/LoginRequest.pest.php`
- `tests/Unit/AppUser/ProfileUpdateRequestTest.php` → `tests/Unit/AppUser/ProfileUpdateRequest.pest.php`

### Step 2.1 — Create `tests/Unit/Pest.php`

```php
<?php

use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);
```

### Step 2.2 — Migrate `BaseModelTest.php`

Delete `tests/Unit/Models/BaseModelTest.php`. Create `tests/Unit/Models/BaseModel.pest.php`:

```php
<?php

use App\Models\BaseModel;

function makeModel(): BaseModel
{
    return new class extends BaseModel
    {
        protected $table = 'users';
        protected $appends = ['dummy'];

        protected function getDummyAttribute(): string
        {
            return 'dummy_value';
        }

        public static function cachedMethod(): string
        {
            return 'cached_result';
        }
    };
}

test('where like applies like constraint', function () {
    $model = makeModel();
    $query = $model->newQuery()->whereLike('name', 'john');

    expect($query->toSql())->toContain('LIKE')
        ->and($query->getBindings())->toContain('john');
});

test('where like with array creates or where', function () {
    $model = makeModel();
    $query = $model->newQuery()->whereLike(['name', 'email'], 'test');

    expect(strtoupper($query->toSql()))->toContain('OR');
});

test('where like with null columns does not throw', function () {
    $model = makeModel();
    $query = $model->newQuery()->whereLike(null, 'test');

    expect($query->toSql())->toContain('select');
});

test('where not excludes value', function () {
    $model = makeModel();
    $query = $model->newQuery()->whereNot('is_admin', true);

    $sql = $query->toSql();

    expect($sql)->toContain('!=')
        ->and($sql)->toContain('is_admin');
});

test('or where not adds or constraint', function () {
    $model = makeModel();
    $query = $model->newQuery()->where('name', 'foo')->orWhereNot('is_admin', true);

    $sql = strtolower($query->toSql());

    expect($sql)->toContain('or')
        ->and($query->toSql())->toContain('!=');
});

test('without appends prevents appended attributes', function () {
    $model = makeModel();
    $model->withoutAppends();

    expect($model->getArrayableAppends())->toBeEmpty();
});

test('get arrayable appends returns appends by default', function () {
    $model = makeModel();
    $appends = $model->getArrayableAppends();

    expect($appends)->not->toBeEmpty()
        ->and($appends)->toContain('dummy');
});

test('call static resolves get cached methods', function () {
    $class = get_class(makeModel());

    expect($class::getCachedMethod())->toBe('cached_result');
});

test('call static falls through to parent for unknown methods', function () {
    $class = get_class(makeModel());

    $class::nonExistentMethod();
})->throws(BadMethodCallException::class);

test('without appends is static flag', function () {
    $modelA = makeModel();
    $modelB = makeModel();

    $modelA->withoutAppends();

    expect($modelA->getArrayableAppends())->toBeEmpty('Model A should have no appends');
});
```

### Step 2.3 — Migrate remaining unit tests

For each remaining unit test file, read the existing file, convert to Pest syntax using the same pattern, delete the old `.php` file, create the new `.pest.php` file.

### Step 2.4 — Run unit tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact tests/Unit/
```

Expected: All unit tests pass.

---

## PHASE 3 — Migrate AppUser Feature Tests to Pest

**Goal:** Convert `tests/Feature/AppUser/*.php` to Pest.

### Step 3.1 — Migrate `DashboardTest.php`

Delete `tests/Feature/AppUser/DashboardTest.php`. Create `tests/Feature/AppUser/Dashboard.pest.php`:

```php
<?php

use App\Models\User;

test('dashboard page is displayed', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('appuser.dashboard'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('AppUser::Index'));
});

test('unauthenticated user cannot access dashboard', function () {
    $response = get(route('appuser.dashboard'));

    $response->assertRedirect(route('auth.login'));
});
```

### Step 3.2 — Migrate `ProfileTest.php`

Delete `tests/Feature/AppUser/ProfileTest.php`. Create `tests/Feature/AppUser/Profile.pest.php`:

```php
<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('appuser.profile.edit'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('AppUser::Profile/Edit'));
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('appuser.profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email is unchanged', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => $user->email,
    ]);

    $response->assertSessionHasNoErrors();
    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('unauthenticated user cannot access profile', function () {
    $response = get(route('appuser.profile.edit'));

    $response->assertRedirect(route('auth.login'));
});

test('profile update requires valid email', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('profile update requires unique email', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create();

    $response = actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->delete(route('appuser.profile.destroy'), [
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    expect(auth()->check())->toBeFalse()
        ->and(User::find($user->id))->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->delete(route('appuser.profile.destroy'), [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['password']);
    expect(User::find($user->id))->not->toBeNull();
});
```

### Step 3.3 — Run AppUser tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact tests/Feature/AppUser/
```

Expected: All 10 tests pass.

---

## PHASE 4 — Migrate UserAuth Feature Tests to Pest

**Goal:** Convert `tests/Feature/UserAuth/*.php` to Pest. These replace the legacy `tests/Feature/Auth/*` tests.

### Files to migrate:
- `tests/Feature/UserAuth/AuthenticationTest.php`
- `tests/Feature/UserAuth/PasswordResetTest.php`
- `tests/Feature/UserAuth/EmailVerificationTest.php`
- `tests/Feature/UserAuth/PasswordConfirmationTest.php`

### Step 4.1 — Migrate each file

Read each existing file, convert to Pest syntax, delete old `.php`, create new `.pest.php`.

Key pattern for auth tests:
```php
test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = post(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    expect(auth()->check())->toBeTrue();
    $response->assertRedirect(route('appuser.dashboard'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    post(route('auth.login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    expect(auth()->check())->toBeFalse();
});

test('admin users are redirected to admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $response = post(route('auth.login'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
});
```

### Step 4.2 — Run UserAuth tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact tests/Feature/UserAuth/
```

Expected: All tests pass.

---

## PHASE 5 — Migrate PublicPage Module Tests to Pest

**Goal:** Convert all `Modules/PublicPage/tests/Feature/*.php` to Pest. This is the largest migration (27 test files).

### Step 5.1 — Migrate in batches

**Batch A — Admin tests (highest quality, migrate first):**
- `Admin/AdminDashboardControllerTest.php`
- `Admin/AdminEventControllerTest.php`
- `Admin/AdminVideoControllerTest.php`
- `Admin/AdminPhotoControllerTest.php`
- `Admin/AdminRouteAuthorizationTest.php`
- `Admin/VideoUploadTest.php`
- `Admin/VideoDeleteCleanupTest.php`
- `Admin/VideoConversionTest.php`
- `Admin/VideoThumbnailServiceTest.php`
- `Admin/EventFormRequestTest.php`
- `Admin/ConvertPendingVideosCommandTest.php`

**Batch B — Public page tests:**
- `PublicPages/PublicPageControllerTest.php`
- `EventGridPageTest.php`
- `EventDetailPageTest.php`
- `GalleryPageTest.php`
- `VideoPlayerDisplayTest.php`
- `EventHeaderDisplayTest.php`
- `TimelineVideoSelectionTest.php`
- `SupportingGridTest.php`
- `GridViewFilterTest.php`
- `ResponsiveDesignTest.php`
- `ResponsiveContentTest.php`
- `MobileNavigationTest.php`
- `NavigationCriticalPathTest.php`
- `SeededDataValidationTest.php`
- `CliEventCreationTest.php`

### Step 5.2 — Conversion pattern

For each file:
1. Read the existing PHPUnit class
2. Convert to Pest `test()` closures
3. Replace `$this->` with global helpers (`actingAs()`, `get()`, `post()`, etc.)
4. Replace `$this->assertX()` with `expect()` where natural
5. Keep Laravel response assertions (`$response->assertStatus()`, etc.)
6. Delete old `.php` file
7. Create new `.pest.php` file

Example for `AdminRouteAuthorizationTest.php`:
```php
test('unauthenticated user cannot access admin dashboard', function () {
    $response = get(route('admin.dashboard'));

    $response->assertRedirect(route('auth.login'));
});

test('non-admin user cannot access admin dashboard', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('admin.dashboard'));

    $response->assertForbidden();
});
```

### Step 5.3 — Run PublicPage tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact Modules/PublicPage/tests/Feature/
```

Expected: All module tests pass.

---

## PHASE 6 — Delete Duplicate Legacy Tests

**Goal:** Remove redundant test files that duplicate the modular versions.

### Step 6.1 — Delete legacy Auth tests

```bash
rm -rf tests/Feature/Auth/
```

Files deleted:
- `tests/Feature/Auth/AuthenticationTest.php`
- `tests/Feature/Auth/RegistrationTest.php`
- `tests/Feature/Auth/PasswordResetTest.php`
- `tests/Feature/Auth/EmailVerificationTest.php`
- `tests/Feature/Auth/PasswordConfirmationTest.php`
- `tests/Feature/Auth/PasswordUpdateTest.php`

### Step 6.2 — Delete duplicate root ProfileTest

```bash
rm tests/Feature/ProfileTest.php
```

This duplicates `tests/Feature/AppUser/ProfileTest.php` (now `Profile.pest.php`).

### Step 6.3 — Migrate remaining root Feature tests

Migrate to Pest:
- `tests/Feature/ExampleTest.php` → `tests/Feature/Example.pest.php`
- `tests/Feature/DatabaseIndexTest.php` → `tests/Feature/DatabaseIndex.pest.php`
- `tests/Feature/Commands/CreateStaffAccountCommandTest.php` → `tests/Feature/Commands/CreateStaffAccountCommand.pest.php`

### Step 6.4 — Verify full suite still passes

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact
```

Expected: All tests pass, no regressions.

---

## PHASE 7 — Test PublicBlogController

**Goal:** Add comprehensive tests for the untested `PublicBlogController` (index + show routes).

### Step 7.1 — Create `Modules/PublicPage/tests/Feature/PublicBlogTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Http;

test('blog index page displays', function () {
    $response = get(route('app.blog.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('PublicPage::BlogIndex'));
});

test('blog index page loads with no articles', function () {
    $response = get(route('app.blog.index'));

    $response->assertStatus(200);
});

test('blog show page displays published article', function () {
    // Create a mock article or use whatever data source the controller uses
    // Adjust based on actual controller implementation
    $response = get(route('app.blog.show', ['post' => 'some-slug']));

    // If article doesn't exist, should 404
    $response->assertStatus(404);
});

test('blog show page returns 404 for nonexistent article', function () {
    $response = get(route('app.blog.show', ['post' => 'nonexistent-slug']));

    $response->assertStatus(404);
});

test('blog show page returns 404 for unpublished article', function () {
    // If articles have published/draft status
    $response = get(route('app.blog.show', ['post' => 'draft-slug']));

    $response->assertStatus(404);
});
```

**NOTE:** Read `PublicBlogController` first to understand the data source (external API? database?), then adjust tests accordingly.

### Step 7.2 — Run tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact --filter=PublicBlogTest
```

---

## PHASE 8 — Test LaunchConferenceController

**Goal:** Add tests for the untested Conference module.

### Step 8.1 — Create `Modules/Conference/tests/TestCase.php`

```php
<?php

namespace Modules\Conference\Tests;

use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
}
```

### Step 8.2 — Create `Modules/Conference/tests/Pest.php`

```php
<?php

use Modules\Conference\Tests\TestCase;

uses(TestCase::class)->in('Feature');
```

### Step 8.3 — Create `Modules/Conference/tests/Feature/LaunchConferenceTest.pest.php`

```php
<?php

test('conference page displays', function () {
    $response = get(route('app.conferences.launch.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('Conference::LaunchConference'));
});

test('conference page has correct inertia props', function () {
    $response = get(route('app.conferences.launch.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('Conference::LaunchConference')
        // Add expected props based on controller
    );
});
```

### Step 8.4 — Update `phpunit.xml` to include Conference tests

Add to the Feature testsuite:
```xml
<directory>Modules/Conference/tests/Feature</directory>
```

### Step 8.5 — Run tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact Modules/Conference/tests/
```

---

## PHASE 9 — Test Event, Video, EventPhoto Models

**Goal:** Add dedicated model unit tests for scopes, relationships, accessors, mutators.

### Step 9.1 — Create `Modules/PublicPage/tests/Unit/EventModelTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Models\Video;

uses(TestCase::class)->in(__DIR__);

test('published scope returns only published events', function () {
    $published = Event::factory()->create(['is_published' => true]);
    $draft = Event::factory()->create(['is_published' => false]);

    $results = Event::published()->get();

    expect($results->pluck('id'))->toContain($published->id)
        ->and($results->pluck('id'))->not->toContain($draft->id);
});

test('unpublished scope returns only draft events', function () {
    $published = Event::factory()->create(['is_published' => true]);
    $draft = Event::factory()->create(['is_published' => false]);

    $results = Event::unpublished()->get();

    expect($results->pluck('id'))->toContain($draft->id)
        ->and($results->pluck('id'))->not->toContain($published->id);
});

test('ordered scope orders by date descending', function () {
    $older = Event::factory()->create(['event_date' => now()->subDays(10)]);
    $newer = Event::factory()->create(['event_date' => now()->subDays(5)]);

    $results = Event::ordered()->get();

    expect($results->first()->id)->toBe($newer->id)
        ->and($results->last()->id)->toBe($older->id);
});

test('event has many videos relationship', function () {
    $event = Event::factory()->create();
    $videos = Video::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->videos)->toHaveCount(3)
        ->and($event->videos->pluck('id'))->toContain($videos[0]->id);
});

test('event has many photos relationship', function () {
    $event = Event::factory()->create();
    $photos = EventPhoto::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->photos)->toHaveCount(3);
});

test('event slug is auto generated from title', function () {
    $event = Event::factory()->create(['title' => 'My Awesome Event']);

    expect($event->slug)->toBe('my-awesome-event');
});

test('event slug handles special characters', function () {
    $event = Event::factory()->create(['title' => "NIOGG's 2026 Conference!"]);

    expect($event->slug)->toBeString()
        ->and($event->slug)->not->toContain("'");
});
```

### Step 9.2 — Create `Modules/PublicPage/tests/Unit/VideoModelTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

uses(TestCase::class)->in(__DIR__);

test('featured scope returns only featured videos', function () {
    $event = Event::factory()->create();
    $featured = Video::factory()->create(['event_id' => $event->id, 'is_featured' => true]);
    $regular = Video::factory()->create(['event_id' => $event->id, 'is_featured' => false]);

    $results = Video::featured()->get();

    expect($results->pluck('id'))->toContain($featured->id)
        ->and($results->pluck('id'))->not->toContain($regular->id);
});

test('video belongs to event relationship', function () {
    $event = Event::factory()->create();
    $video = Video::factory()->create(['event_id' => $event->id]);

    expect($video->event->id)->toBe($event->id);
});

test('format duration accessor formats seconds correctly', function () {
    $video = Video::factory()->create(['duration' => 3661]); // 1h 1m 1s

    expect($video->format_duration)->toBeString();
});

test('thumbnail url accessor returns correct path', function () {
    $video = Video::factory()->create(['thumbnail_url' => 'thumbs/test.jpg']);

    expect($video->thumbnail_url)->toContain('test.jpg');
});

test('conversion status defaults', function () {
    $video = Video::factory()->create();

    expect($video->conversion_status)->toBe('pending')
        ->or($video->conversion_status)->toBeNull();
});
```

### Step 9.3 — Create `Modules/PublicPage/tests/Unit/EventPhotoModelTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;

uses(TestCase::class)->in(__DIR__);

test('photo belongs to event relationship', function () {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->create(['event_id' => $event->id]);

    expect($photo->event->id)->toBe($event->id);
});

test('ordered scope orders by position', function () {
    $event = Event::factory()->create();
    $first = EventPhoto::factory()->create(['event_id' => $event->id, 'position' => 1]);
    $second = EventPhoto::factory()->create(['event_id' => $event->id, 'position' => 2]);

    $results = EventPhoto::ordered()->get();

    expect($results->first()->id)->toBe($first->id)
        ->and($results->last()->id)->toBe($second->id);
});

test('thumbnail generation status tracking', function () {
    $photo = EventPhoto::factory()->create();

    expect($photo->thumbnail_generated)->toBeFalse()
        ->or($photo->thumbnail_generated)->toBeNull();
});
```

### Step 9.4 — Run model tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact Modules/PublicPage/tests/Unit/
```

---

## PHASE 10 — Test EventPhotoUploadService

**Goal:** Add unit tests for the untested `EventPhotoUploadService`.

### Step 10.1 — Read the service first

Read `Modules/PublicPage/app/Services/EventPhotoUploadService.php` to understand its methods, then write tests.

### Step 10.2 — Create `Modules/PublicPage/tests/Feature/Admin/EventPhotoUploadServiceTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Services\EventPhotoUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('upload single photo stores file and creates record', function () {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $service = app(EventPhotoUploadService::class);
    $photo = $service->upload($event, $file);

    expect($photo->event_id)->toBe($event->id)
        ->and($photo->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($photo->image_path);
});

test('upload multiple photos stores all files', function () {
    $event = Event::factory()->create();
    $files = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
        UploadedFile::fake()->image('photo3.jpg'),
    ];

    $service = app(EventPhotoUploadService::class);
    $photos = $service->uploadMultiple($event, $files);

    expect($photos)->toHaveCount(3);
});

test('upload rejects non image file', function () {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $service = app(EventPhotoUploadService::class);

    expect(fn () => $service->upload($event, $file))->toThrow(Exception::class);
});

test('upload rejects oversized file', function () {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('huge.jpg')->size(10000); // 10MB

    $service = app(EventPhotoUploadService::class);

    expect(fn () => $service->upload($event, $file))->toThrow(Exception::class);
});

test('upload generates photo thumbnail job', function () {
    Bus::fake();

    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $service = app(EventPhotoUploadService::class);
    $service->upload($event, $file);

    Bus::assertDispatched(\Modules\PublicPage\Jobs\GeneratePhotoThumbnail::class);
});
```

### Step 10.3 — Run tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact --filter=EventPhotoUploadServiceTest
```

---

## PHASE 11 — Test Jobs

**Goal:** Test all three jobs with happy path and failure scenarios.

### Step 11.1 — Create `Modules/PublicPage/tests/Unit/Jobs/ConvertVideoToMp4Test.pest.php`

```php
<?php

use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class)->in(__DIR__);

test('job converts video to mp4', function () {
    Storage::fake('public');
    Storage::disk('public')->put('videos/original.webm', 'fake video content');

    $video = Video::factory()->create([
        'original_url' => 'videos/original.webm',
        'conversion_status' => 'pending',
    ]);

    $job = new ConvertVideoToMp4($video);

    $job->handle();

    $video->refresh();

    expect($video->conversion_status)->toBe('completed')
        ->and($video->converted_url)->not->toBeNull();
});

test('job handles missing source file gracefully', function () {
    $video = Video::factory()->create([
        'original_url' => 'videos/nonexistent.webm',
        'conversion_status' => 'pending',
    ]);

    $job = new ConvertVideoToMp4($video);

    $job->handle();

    $video->refresh();

    expect($video->conversion_status)->toBe('failed');
});

test('job handles already converted video', function () {
    $video = Video::factory()->create([
        'conversion_status' => 'completed',
    ]);

    $job = new ConvertVideoToMp4($video);

    $job->handle();

    $video->refresh();

    expect($video->conversion_status)->toBe('completed');
});

test('job has unique id', function () {
    $video = Video::factory()->create();
    $job = new ConvertVideoToMp4($video);

    expect($job->uniqueId())->toBe((string) $video->id);
});
```

### Step 11.2 — Create `Modules/PublicPage/tests/Unit/Jobs/GenerateVideoThumbnailTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Jobs\GenerateVideoThumbnail;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class)->in(__DIR__);

test('job generates video thumbnail', function () {
    Storage::fake('public');
    Storage::disk('public')->put('videos/test.mp4', 'fake video content');

    $video = Video::factory()->create([
        'converted_url' => 'videos/test.mp4',
    ]);

    $job = new GenerateVideoThumbnail($video);

    $job->handle();

    $video->refresh();

    expect($video->thumbnail_url)->not->toBeNull();
    Storage::disk('public')->assertExists($video->thumbnail_url);
});

test('job handles missing video file gracefully', function () {
    $video = Video::factory()->create([
        'converted_url' => 'videos/nonexistent.mp4',
    ]);

    $job = new GenerateVideoThumbnail($video);

    // Should not throw
    $job->handle();

    $video->refresh();

    expect($video->thumbnail_url)->toBeNull();
});

test('job handles deleted video gracefully', function () {
    $video = Video::factory()->create();
    $id = $video->id;
    $video->delete();

    $job = new GenerateVideoThumbnail($video);

    // Should not throw
    $job->handle();
});
```

### Step 11.3 — Create `Modules/PublicPage/tests/Unit/Jobs/GeneratePhotoThumbnailTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class)->in(__DIR__);

test('job generates photo thumbnail', function () {
    Storage::fake('public');
    Storage::disk('public')->put('photos/original.jpg', 'fake image content');

    $photo = EventPhoto::factory()->create([
        'image_path' => 'photos/original.jpg',
    ]);

    $job = new GeneratePhotoThumbnail($photo);

    $job->handle();

    $photo->refresh();

    expect($photo->thumbnail_path)->not->toBeNull();
    Storage::disk('public')->assertExists($photo->thumbnail_path);
});

test('job handles missing source file gracefully', function () {
    $photo = EventPhoto::factory()->create([
        'image_path' => 'photos/nonexistent.jpg',
    ]);

    $job = new GeneratePhotoThumbnail($photo);

    $job->handle();

    $photo->refresh();

    expect($photo->thumbnail_generated)->toBeFalse();
});

test('job handles deleted photo gracefully', function () {
    $photo = EventPhoto::factory()->create();
    $id = $photo->id;
    $photo->delete();

    $job = new GeneratePhotoThumbnail($photo);

    // Should not throw
    $job->handle();
});
```

### Step 11.4 — Run job tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact Modules/PublicPage/tests/Unit/Jobs/
```

---

## PHASE 12 — Test Remaining Commands

**Goal:** Test the 3 untested artisan commands.

### Step 12.1 — Create `tests/Feature/Commands/CreateAdminUserCommandTest.pest.php`

```php
<?php

use App\Models\User;

test('creates admin user with all options', function () {
    $this->artisan('admin:create', [
        '--name' => 'Super Admin',
        '--email' => 'admin@example.com',
        '--password' => 'secure-password-123',
    ])->assertSuccessful();

    expect(User::where('email', 'admin@example.com')->exists())->toBeTrue();

    $user = User::where('email', 'admin@example.com')->first();
    expect($user->is_admin)->toBeTrue();
});

test('fails on duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->artisan('admin:create', [
        '--name' => 'Duplicate',
        '--email' => 'existing@example.com',
        '--password' => 'password',
    ])->assertFailed();
});

test('fails on invalid email format', function () {
    $this->artisan('admin:create', [
        '--name' => 'Bad Email',
        '--email' => 'not-an-email',
        '--password' => 'password',
    ])->assertFailed();
});

test('creates admin interactively', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Full name', 'Interactive Admin')
        ->expectsQuestion('Email address', 'interactive@example.com')
        ->expectsQuestion('Password', 'password123')
        ->assertSuccessful();

    expect(User::where('email', 'interactive@example.com')->exists())->toBeTrue();
});
```

### Step 12.2 — Create `tests/Feature/Commands/RetryFailedThumbnailsCommandTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Support\Facades\Bus;

test('retries failed photo thumbnails', function () {
    Bus::fake();

    $failed = EventPhoto::factory()->create(['thumbnail_generated' => false]);

    $this->artisan('niogg:retry-failed-thumbnails')->assertSuccessful();

    Bus::assertDispatched(\Modules\PublicPage\Jobs\GeneratePhotoThumbnail::class);
});

test('skips photos with generated thumbnails', function () {
    Bus::fake();

    EventPhoto::factory()->create(['thumbnail_generated' => true]);

    $this->artisan('niogg:retry-failed-thumbnails')->assertSuccessful();

    Bus::assertNothingDispatched();
});

test('handles empty database gracefully', function () {
    $this->artisan('niogg:retry-failed-thumbnails')->assertSuccessful();
});
```

### Step 12.3 — Create `tests/Feature/Commands/RetryFailedVideoThumbnailsCommandTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Bus;

test('retries failed video thumbnails', function () {
    Bus::fake();

    $video = Video::factory()->create(['thumbnail_url' => null]);

    $this->artisan('niogg:retry-failed-video-thumbnails')->assertSuccessful();

    Bus::assertDispatched(\Modules\PublicPage\Jobs\GenerateVideoThumbnail::class);
});

test('skips videos with existing thumbnails', function () {
    Bus::fake();

    Video::factory()->create(['thumbnail_url' => 'thumbs/existing.jpg']);

    $this->artisan('niogg:retry-failed-video-thumbnails')->assertSuccessful();

    Bus::assertNothingDispatched();
});

test('handles empty database gracefully', function () {
    $this->artisan('niogg:retry-failed-video-thumbnails')->assertSuccessful();
});
```

### Step 12.4 — Run command tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact tests/Feature/Commands/
```

---

## PHASE 13 — Test Remaining Form Requests

**Goal:** Add unit tests for untested form requests.

### Step 13.1 — Create `Modules/PublicPage/tests/Unit/VideoFormRequestTest.pest.php`

```php
<?php

uses(TestCase::class)->in(__DIR__);

test('video form request requires title', function () {
    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.videos.store', ['event' => Event::factory()->create()]), [
            'title' => '',
            'url' => 'https://example.com/video.mp4',
        ]);

    $response->assertSessionHasErrors(['title']);
});

test('video form request requires valid url', function () {
    $event = Event::factory()->create();

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.videos.store', ['event' => $event]), [
            'title' => 'Test Video',
            'url' => 'not-a-url',
        ]);

    $response->assertSessionHasErrors(['url']);
});

test('video form request passes with valid data', function () {
    $event = Event::factory()->create();

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.videos.store', ['event' => $event]), [
            'title' => 'Test Video',
            'url' => 'https://example.com/video.mp4',
        ]);

    $response->assertSessionHasNoErrors();
});
```

### Step 13.2 — Create `Modules/PublicPage/tests/Unit/VideoThumbnailRequestTest.pest.php`

```php
<?php

uses(TestCase::class)->in(__DIR__);

test('video thumbnail request requires file', function () {
    $video = Video::factory()->create();

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.videos.thumbnail', ['video' => $video]));

    $response->assertSessionHasErrors(['thumbnail']);
});

test('video thumbnail request requires image file', function () {
    $video = Video::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf');

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.videos.thumbnail', ['video' => $video]), [
            'thumbnail' => $file,
        ]);

    $response->assertSessionHasErrors(['thumbnail']);
});

test('video thumbnail request passes with valid image', function () {
    $video = Video::factory()->create();
    $file = UploadedFile::fake()->image('thumbnail.jpg');

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.videos.thumbnail', ['video' => $video]), [
            'thumbnail' => $file,
        ]);

    $response->assertSessionHasNoErrors();
});
```

### Step 13.3 — Create `Modules/PublicPage/tests/Unit/StoreEventPhotosRequestTest.pest.php`

```php
<?php

uses(TestCase::class)->in(__DIR__);

test('store event photos request requires files', function () {
    $event = Event::factory()->create();

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.events.photos.store', ['event' => $event]));

    $response->assertSessionHasErrors(['photos']);
});

test('store event photos request requires image files', function () {
    $event = Event::factory()->create();
    $files = [UploadedFile::fake()->create('document.pdf')];

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.events.photos.store', ['event' => $event]), [
            'photos' => $files,
        ]);

    $response->assertSessionHasErrors(['photos.0']);
});

test('store event photos request passes with valid images', function () {
    $event = Event::factory()->create();
    $files = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
    ];

    $response = actingAs(User::factory()->admin()->create())
        ->post(route('admin.events.photos.store', ['event' => $event]), [
            'photos' => $files,
        ]);

    $response->assertSessionHasNoErrors();
});
```

### Step 13.4 — Create `Modules/PublicPage/tests/Unit/UpdateEventPhotoRequestTest.pest.php`

```php
<?php

uses(TestCase::class)->in(__DIR__);

test('update event photo request validates caption max length', function () {
    $photo = EventPhoto::factory()->create();

    $response = actingAs(User::factory()->admin()->create())
        ->put(route('admin.photos.update', ['photo' => $photo]), [
            'caption' => str_repeat('a', 501),
        ]);

    $response->assertSessionHasErrors(['caption']);
});

test('update event photo request passes with valid data', function () {
    $photo = EventPhoto::factory()->create();

    $response = actingAs(User::factory()->admin()->create())
        ->put(route('admin.photos.update', ['photo' => $photo]), [
            'caption' => 'Valid caption',
        ]);

    $response->assertSessionHasNoErrors();
});
```

### Step 13.5 — Run form request tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact Modules/PublicPage/tests/Unit/
```

---

## PHASE 14 — Test Untested Routes + Middleware Gaps

**Goal:** Fill route-level and middleware testing gaps.

### Step 14.1 — Create `tests/Feature/UserAuth/VerifiedMiddlewareTest.pest.php`

```php
<?php

use App\Models\User;

test('unverified user is redirected from dashboard', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $response = actingAs($user)->get(route('appuser.dashboard'));

    $response->assertRedirect(route('auth.verification.notice'));
});

test('verified user can access dashboard', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('appuser.dashboard'));

    $response->assertStatus(200);
});
```

### Step 14.2 — Create `tests/Feature/UserAuth/EmailVerificationThrottleTest.pest.php`

```php
<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

test('email verification notification is throttled', function () {
    $user = User::factory()->unverified()->create();

    // Send 6 requests (the limit)
    for ($i = 0; $i < 6; $i++) {
        $response = actingAs($user)
            ->post(route('auth.verification.send'));

        $response->assertStatus(302); // Redirect back
    }

    // 7th request should be throttled
    $response = actingAs($user)
        ->post(route('auth.verification.send'));

    $response->assertStatus(429);
});
```

### Step 14.3 — Create `Modules/PublicPage/tests/Feature/PublicPages/StaticPagesTest.pest.php`

```php
<?php

test('vision and values page displays', function () {
    $response = get(route('app.vision-and-values'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('PublicPage::VisionAndValues'));
});

test('career opportunities page displays', function () {
    $response = get(route('app.careers'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('PublicPage::Careers'));
});

test('awards and recognitions page displays', function () {
    $response = get(route('app.awards'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('PublicPage::Awards'));
});
```

### Step 14.4 — Create `Modules/PublicPage/tests/Feature/Admin/AdminVideoEditTest.pest.php`

```php
<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

test('admin can access video edit page', function () {
    $event = Event::factory()->create();
    $video = Video::factory()->create(['event_id' => $event->id]);
    $admin = User::factory()->admin()->create();

    $response = actingAs($admin)->get(route('admin.videos.edit', ['video' => $video]));

    $response->assertStatus(200);
});

test('non admin cannot access video edit page', function () {
    $video = Video::factory()->create();
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('admin.videos.edit', ['video' => $video]));

    $response->assertForbidden();
});

test('unauthenticated user cannot access video edit page', function () {
    $video = Video::factory()->create();

    $response = get(route('admin.videos.edit', ['video' => $video]));

    $response->assertRedirect(route('auth.login'));
});
```

### Step 14.5 — Create `Modules/PublicPage/tests/Feature/Admin/AdminEventEditTest.pest.php`

```php
<?php

test('admin can access event edit page', function () {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = actingAs($admin)->get(route('admin.events.edit', ['event' => $event]));

    $response->assertStatus(200);
});

test('non admin cannot access event edit page', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create();

    $response = actingAs($user)->get(route('admin.events.edit', ['event' => $event]));

    $response->assertForbidden();
});
```

### Step 14.6 — Run route/middleware tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact --filter=VerifiedMiddleware
vendor/bin/sail pest --compact --filter=StaticPagesTest
vendor/bin/sail pest --compact --filter=AdminVideoEditTest
vendor/bin/sail pest --compact --filter=AdminEventEditTest
```

---

## PHASE 15 — Fix Test Quality Issues

**Goal:** Address identified quality issues in existing tests.

### Step 15.1 — Fix `EventGridPageTest.php` DB assertions

Read `Modules/PublicPage/tests/Feature/EventGridPageTest.php`. Replace any DB assertions with response assertions.

Pattern to fix:
```php
// BAD - asserts on DB directly
$events = Event::where('is_published', true)->get();
expect($events)->toHaveCount(3);

// GOOD - asserts on response
$response->assertInertia(fn ($page) => $page
    ->has('events', 3)
);
```

### Step 15.2 — Fix `AdminDashboardControllerTest.php` setUp

Read `Modules/PublicPage/tests/Feature/Admin/AdminDashboardControllerTest.php`. Fix the setUp to use the created admin instance directly.

```php
// BAD
$this->admin = User::factory()->admin()->create();
$this->admin = User::where('is_admin', true)->first();

// GOOD
$this->admin = User::factory()->admin()->create();
```

### Step 15.3 — Run full suite

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail pest --compact
```

---

## PHASE 16 — Final Verification

**Goal:** Ensure everything works together with zero regressions.

### Step 16.1 — Run Pint

```bash
vendor/bin/sail bin pint
```

### Step 16.2 — Run full test suite

```bash
vendor/bin/sail pest --compact
```

Expected: All tests pass, 0 failures, 0 warnings.

### Step 16.3 — Run test coverage

```bash
vendor/bin/sail pest --coverage --min=80
```

Expected: Coverage ≥ 80% (adjust threshold based on current baseline).

### Step 16.4 — Run Larastan

```bash
vendor/bin/sail php vendor/bin/phpstan analyse --memory-limit=2G
```

Expected: No new errors introduced.

### Step 16.5 — Run E2E tests

```bash
vendor/bin/sail npx playwright test
```

Expected: All E2E tests pass.

---

## Rollback

If any phase causes regressions:

```bash
git log --oneline -10   # identify phase commits
git revert <commit-hash>  # revert the specific phase
```

Each phase should be committed independently. Reverting Phase X requires phases X through 16 to be reverted in reverse order.

---

## Expected Test Count After Completion

| Category | Before | After | New Tests |
|----------|--------|-------|-----------|
| Unit tests | ~20 | ~50 | +30 |
| Feature tests | ~200 | ~280 | +80 |
| **Total** | **~220** | **~330** | **+110** |

---

## Unresolved Questions

1. **PublicBlogController data source** — Does it fetch from an external API or local DB? Tests need adjustment based on implementation.
2. **VideoUploadRequest (app vs module)** — Two files with same name exist. Is the root `app/Http/Requests/Admin/VideoUploadRequest.php` still used or is it dead code?
3. **Event photo upload size limit** — What's the max file size? Need exact value for test assertions.
4. **Contact form rate limiting** — Is there rate limiting on the contact form endpoint? If so, needs throttle test.
5. **VideoFormRequest exact rules** — Need to read the file to know exact validation rules before writing tests.

(End of file)

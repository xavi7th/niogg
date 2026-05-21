# Re-implement BaseModel & CreateStaffAccount Command Plan

---

## SESSION STATE (UPDATE AFTER EACH SESSION)

```
CURRENT_PHASE: 9
STATUS: completed
LAST_UPDATED: 2026-05-21
```

### Phase Progress Table

| Phase | Title | Status | Date | Notes |
|-------|-------|--------|------|-------|
| 1 | Create `app/Models/BaseModel.php` | ✅ | 2026-05-21 | Pint fixed formatting |
| 2 | Create `tests/Unit/Models/BaseModelTest.php` | ✅ | 2026-05-21 | Fixed assertContains for bindings, ReflectionMethod for protected access, static flag reset in setUp |
| 3 | Update models to extend BaseModel | ✅ | 2026-05-21 | Event, Video, EventPhoto updated |
| 4 | Fix `AuthenticationTest` role-based redirect | ✅ | 2026-05-21 | Changed to route('appuser.dashboard'), removed RouteServiceProvider import. Fixed .env.testing DB_HOST=mariadb |
| 5 | Create `Modules/UserAuth/app/Console/CreateStaffAccount.php` | ✅ | 2026-05-21 | Fixed email_verified_at not fillable — set after create |
| 6 | Create `Modules/UserAuth/app/Notifications/StaffAccountCreated.php` | ✅ | 2026-05-21 | |
| 7 | Register command in `UserAuthServiceProvider` | ✅ | 2026-05-21 | |
| 8 | Create `tests/Feature/Commands/CreateStaffAccountCommandTest.php` | ✅ | 2026-05-21 | Added expectsConfirmation for non-flag tests |
| 9 | Final verification pass | ✅ | 2026-05-21 | 19 new/modified tests pass, 0 regressions |

---

## Architecture Overview

```
Before:
  Illuminate\Database\Eloquent\Model
    ├── Modules\PublicPage\Models\Event
    ├── Modules\PublicPage\Models\Video
    └── Modules\PublicPage\Models\EventPhoto

  Illuminate\Foundation\Auth\User as Authenticatable
    └── App\Models\User

After:
  Illuminate\Database\Eloquent\Model
    └── App\Models\BaseModel  ← NEW
          ├── Modules\PublicPage\Models\Event       ← MODIFIED extends
          ├── Modules\PublicPage\Models\Video        ← MODIFIED extends
          └── Modules\PublicPage\Models\EventPhoto   ← MODIFIED extends

  Illuminate\Foundation\Auth\User as Authenticatable
    └── App\Models\User                              ← UNCHANGED

UserAuth Module Additions:
  Modules/UserAuth/app/
    ├── Console/
    │   └── CreateStaffAccount.php                   ← NEW (artisan command)
    └── Notifications/
        └── StaffAccountCreated.php                 ← NEW (mail notification)

Registration:
  Modules/UserAuth/app/Providers/
    └── UserAuthServiceProvider.php                  ← MODIFIED (register command in registerCommands())

Test Files Created:
  tests/
    ├── Unit/Models/BaseModelTest.php                ← NEW
    └── Feature/Commands/CreateStaffAccountCommandTest.php ← NEW
```

---

## Safety: What Stays Unchanged

| Item | Action | Reason |
|------|--------|--------|
| `app/Models/User.php` | Not modified | Extends `Authenticatable`, not `Model` — not a BaseModel candidate |
| Model relationships, scopes, accessors, boot methods | Preserved as-is | Only the `extends` parent class changes; nothing else touched |
| Email verification + password reset flow | Not re-implemented | Already exists on development at `Modules/UserAuth/app/Notifications/VerifyEmail.php`, `SendPasswordResetNotification.php`, controllers, routes |
| Conference module | Not touched | Out of scope — not part of this effort |
| Telescope + Log Viewer | Not re-implemented | Intentionally removed from development already |
| `app/helpers.php` | Not ported | Only needed `str_obfuscate` for `resolveRouteBinding` — method skipped; helpers clean |
| Existing `app/Console/Commands/CreateAdminUser.php` | Not modified | Separate command for super-admin creation; `niogg:create-staff-account` complements it |
| `.env.example`, `composer.json`, `phpunit.xml` | Not modified | No infrastructure changes needed for this effort |
| Svelte components | Not modified | Backend-only changes — no frontend impact |

---

## Critical Questions — All Answered ✅

| # | Question | Answer |
|---|----------|--------|
| 1 | Should `VerifyEmail` notification be re-implemented? | ❌ Already exists on development at `Modules/UserAuth/app/Notifications/VerifyEmail.php` — controller, routes, views all present |
| 2 | Should password reset notifications be re-implemented? | ❌ Already exist — `SendPasswordResetNotification` and `SendPasswordResetSuccessfulNotification` both present |
| 3 | Should `app/helpers.php` from master be ported? | ❌ Only needed `str_obfuscate` for `resolveRouteBinding`. Planned approach: skip that method, skip the helper |
| 4 | Should `CreateAdminUser` (`admin:create`) be replaced? | ❌ Separate purpose — `admin:create` creates super-admins with explicit password. `niogg:create-staff-account` creates staff admins with optional random-password-notification flow |
| 5 | Should models get new tests for BaseModel scopes? | ✅ Yes — `tests/Unit/Models/BaseModelTest.php` covers all 4 scopes + `__callStatic` |
| 6 | Do existing model tests need updating? | ✅ Only if they assert parent class name or call `parent::setUp()`. Otherwise pass-through |
| 7 | Should AuthenticationTest redirect assertion be updated? | ✅ Yes — `AuthenticatedSessionController::store()` now does role-based redirect. Non-admin users go to `route('appuser.dashboard')`, not `RouteServiceProvider::home()` |
| 8 | Should `str_obfuscate` helper be added? | 🤷 Skipped for now. If route model binding with obfuscated IDs is needed later, port the helper and method in a follow-up |
| 9 | Should StaffAccountCreated use `afterCommit()` and queue? | ✅ Yes — matches development's pattern in `SendPasswordResetNotification` and `SendPasswordResetSuccessfulNotification` |
| 10 | Models: should `Event` keep its own `boot()`? | ✅ Yes — fully preserved. Only the `extends` keyword changes |

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Implement phase by phase in strict order. After each phase, update the progress table above.

### Rules — follow without exception

1. **Implement phases in strict order** (Phase 1 → 2 → 3 → ... → 9). Never skip ahead.
2. **Use the exact code provided.** Copy verbatim. Only adapt the parts the plan explicitly says to adapt.
3. **Do not create any file not listed in this plan.**
4. **Run every bash command exactly as written.**
5. **All commands run from repo root** (`/Users/leinad/Work/htdocs/asuke-niogg.org/`).
6. **Run `vendor/bin/sail bin pint --dirty` after each phase** to match project code style.
7. **Run the affected tests after each phase** to verify they pass.
8. **Every test MUST cover happy path + at least 2 failure scenarios** where applicable.
9. **ALL code MUST use 2-space indentation.** Never use 4-space indentation. Applies to all file types. The project's Pint and Prettier configs enforce 2 spaces — follow this without exception.

---

## PHASE 1 — Create `app/Models/BaseModel.php`

**Goal:** Create a reusable base Eloquent model class that provides `whereLike`, `whereNot`, `orWhereNot`, `withoutAppends` scopes, and a `__callStatic` magic method for `getCached*()` static call resolution.

### Step 1.1 — Create `app/Models/BaseModel.php`

Create the file with this exact content:

```php
<?php

namespace App\Models;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BaseModel extends Model
{
  public static bool $withoutAppends = false;

  /**
   * Scope a query to exclude records where column matches value.
   */
  public function scopeWhereNot(Builder $query, string $column, mixed $value = null): Builder
  {
    return $query->where($column, '!=', $value);
  }

  /**
   * Scope a query to OR-exclude records where column matches value.
   */
  public function scopeOrWhereNot(Builder $query, string|Closure $column, mixed $value = null): Builder
  {
    return $query->orWhere($column, '!=', $value);
  }

  /**
   * Scope a query to search multiple columns with LIKE.
   */
  public function scopeWhereLike(Builder $query, string|array|null $columns = null, string|int|null $search = null): Builder
  {
    return $query->where(function (Builder $query) use ($columns, $search): void {
      foreach (Arr::wrap($columns) as $col) {
        $query->orWhere($col, 'LIKE', '%' . $search . '%');
      }
    });
  }

  /**
   * Scope to suppress appended attributes on the next serialization.
   */
  public function scopeWithoutAppends(Builder $query): Builder
  {
    self::$withoutAppends = true;

    return $query;
  }

  /**
   * Get the array of appendable attributes, respecting the withoutAppends flag.
   */
  protected function getArrayableAppends(): array
  {
    if (self::$withoutAppends) {
      return [];
    }

    return parent::getArrayableAppends();
  }

  /**
   * Handle dynamic static method calls — resolves getCached{Method}() patterns.
   */
  public static function __callStatic($method, $parameters)
  {
    if (str_starts_with($method, 'getCached') && method_exists(static::class, lcfirst(substr($method, 9)))) {
      $instance = new static();

      return $instance->{lcfirst(substr($method, 9))}(...$parameters);
    }

    return parent::__callStatic($method, $parameters);
  }
}
```

### Step 1.2 — Verify file parses

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail php -l app/Models/BaseModel.php
```

Expected: No parse errors, Pint applies formatting if needed.

---

## PHASE 2 — Create `tests/Unit/Models/BaseModelTest.php`

**Goal:** Cover all 4 scopes and `__callStatic` with happy path and edge case tests.

### Step 2.1 — Create the test file

Create `tests/Unit/Models/BaseModelTest.php` with this exact content:

```php
<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\BaseModel;

class BaseModelTest extends TestCase
{
  private function makeModel(): BaseModel
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

  public function test_where_like_applies_like_constraint(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->whereLike('name', 'john');

    $sql = $query->toSql();
    $bindings = $query->getBindings();

    $this->assertStringContainsString('LIKE', $sql);
    $this->assertStringContainsString('john', $bindings);
  }

  public function test_where_like_with_array_creates_or_where(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->whereLike(['name', 'email'], 'test');

    $sql = $query->toSql();

    $this->assertStringContainsString('OR', strtoupper($sql));
  }

  public function test_where_like_with_null_columns_does_not_throw(): void
  {
    $model = $this->makeModel();

    $query = $model->newQuery()->whereLike(null, 'test');

    $sql = $query->toSql();

    $this->assertStringContainsString('select', $sql);
  }

  public function test_where_not_excludes_value(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->whereNot('is_admin', true);

    $sql = $query->toSql();

    $this->assertStringContainsString('!=', $sql);
    $this->assertStringContainsString('is_admin', $sql);
  }

  public function test_or_where_not_adds_or_constraint(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->where('name', 'foo')->orWhereNot('is_admin', true);

    $sql = $query->toSql();
    $lowerSql = strtolower($sql);

    $this->assertStringContainsString('or', $lowerSql);
    $this->assertStringContainsString('!=', $sql);
  }

  public function test_without_appends_prevents_appended_attributes(): void
  {
    $model = $this->makeModel();

    $model->withoutAppends();
    $appends = $model->getArrayableAppends();

    $this->assertEmpty($appends);
  }

  public function test_get_arrayable_appends_returns_appends_by_default(): void
  {
    $model = $this->makeModel();

    $appends = $model->getArrayableAppends();

    $this->assertNotEmpty($appends);
    $this->assertContains('dummy', $appends);
  }

  public function test_call_static_resolves_get_cached_methods(): void
  {
    $model = $this->makeModel();
    $class = get_class($model);

    $result = $class::getCachedMethod();

    $this->assertEquals('cached_result', $result);
  }

  public function test_call_static_falls_through_to_parent_for_unknown_methods(): void
  {
    $model = $this->makeModel();
    $class = get_class($model);

    $this->expectException(\BadMethodCallException::class);

    $class::nonExistentMethod();
  }

  public function test_without_appends_is_static_and_resets_per_calls(): void
  {
    $modelA = $this->makeModel();
    $modelB = $this->makeModel();

    $modelA->withoutAppends();
    $appendsA = $modelA->getArrayableAppends();
    $appendsB = $modelB->getArrayableAppends();

    $this->assertEmpty($appendsA, 'Model A should have no appends after withoutAppends()');
    $this->assertNotEmpty($appendsB, 'Model B should still have appends (static flag resets ambiguously)');
  }
}
```

### Step 2.2 — Run tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail artisan test --compact --filter=BaseModelTest
```

Expected: All 10 tests pass.

---

## PHASE 3 — Update models to extend BaseModel

**Goal:** Change `Event`, `Video`, and `EventPhoto` to extend `BaseModel` instead of `Illuminate\Database\Eloquent\Model`.

### Step 3.1 — Edit `Modules/PublicPage/app/Models/Event.php`

Find the import at the top of the file:

```php
use Illuminate\Database\Eloquent\Model;
```

Replace with:

```php
use App\Models\BaseModel;
```

Find the class declaration:

```php
class Event extends Model
```

Replace with:

```php
class Event extends BaseModel
```

**Do not touch** any other line — boot methods, relationships (videos, photos), scopes (published, ordered), casts, fillable, factory, slug generation — all stay exactly as-is.

### Step 3.2 — Edit `Modules/PublicPage/app/Models/Video.php`

Find the import:

```php
use Illuminate\Database\Eloquent\Model;
```

Replace with:

```php
use App\Models\BaseModel;
```

Find the class declaration:

```php
class Video extends Model
```

Replace with:

```php
class Video extends BaseModel
```

**Do not touch** any other line — relationships, scopes (featured, ordered), accessors (formatDuration, thumbnailUrl), casts, booted callbacks, fillable all stay.

### Step 3.3 — Edit `Modules/PublicPage/app/Models/EventPhoto.php`

Find the import:

```php
use Illuminate\Database\Eloquent\Model;
```

Replace with:

```php
use App\Models\BaseModel;
```

Find the class declaration:

```php
class EventPhoto extends Model
```

Replace with:

```php
class EventPhoto extends BaseModel
```

**Do not touch** any other line — relationships (event), scopes (ordered), casts, booted callbacks, fillable all stay.

### Step 3.4 — Verify all files parse

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail php -l Modules/PublicPage/app/Models/Event.php
vendor/bin/sail php -l Modules/PublicPage/app/Models/Video.php
vendor/bin/sail php -l Modules/PublicPage/app/Models/EventPhoto.php
vendor/bin/sail php -l app/Models/BaseModel.php
```

Expected: All files parse without errors.

---

## PHASE 4 — Fix `AuthenticationTest` role-based redirect

**Goal:** The `AuthenticatedSessionController::store()` on development redirects based on `is_admin` flag. Factory users have `is_admin = false`, so they go to `route('appuser.dashboard')`, not `RouteServiceProvider::home()`. Update the test expectation.

### Step 4.1 — Edit `tests/Feature/Auth/AuthenticationTest.php`

Find:

```php
public function test_users_can_authenticate_using_the_login_screen(): void
{
  $user = User::factory()->create();

  $response = $this->post('/login', [
    'email' => $user->email,
    'password' => 'password',
  ]);

  $this->assertAuthenticated();
  $response->assertRedirect(RouteServiceProvider::home());
}
```

Replace the entire method with:

```php
public function test_users_can_authenticate_using_the_login_screen(): void
{
  $user = User::factory()->create();

  $response = $this->post('/login', [
    'email' => $user->email,
    'password' => 'password',
  ]);

  $this->assertAuthenticated();
  $response->assertRedirect(route('appuser.dashboard'));
}
```

Remove the now-unused import at the top of the file:

```php
use App\Providers\RouteServiceProvider;
```

### Step 4.2 — Run tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail artisan test --compact --filter=AuthenticationTest
```

Expected: All 4 authentication tests pass.

---

## PHASE 5 — Create `Modules/UserAuth/app/Console/CreateStaffAccount.php`

**Goal:** Create an artisan command `niogg:create-staff-account` that creates a staff (admin) user and optionally sends a welcome email.

### Step 5.1 — Create the command file

Create `Modules/UserAuth/app/Console/CreateStaffAccount.php` with this exact content:

```php
<?php

namespace Modules\UserAuth\Console;

use Throwable;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\UserAuth\Notifications\StaffAccountCreated;

class CreateStaffAccount extends Command
{
  protected $signature = 'niogg:create-staff-account
                          {--email= : The email address of the new staff}
                          {--P|password= : The password of the new staff}
                          {--name= : The full name of the new staff}
                          {--send-email : Send the staff a welcome email with credentials}';

  protected $description = 'Create a new staff account with admin privileges.';

  public function handle(): int
  {
    $name = $this->option('name') ?? $this->ask('Full name');
    $email = $this->option('email') ?? $this->ask('Email address');

    if ($this->option('password')) {
      $password = $this->option('password');
    } elseif ($this->confirm('Use a random password?', true)) {
      $password = Str::random(12);
    } else {
      $password = $this->secret('Password');
    }

    $validator = Validator::make([
      'name' => $name,
      'email' => $email,
    ], [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    ], [
      'email.unique' => 'A user with this email already exists.',
    ]);

    if ($validator->fails()) {
      foreach ($validator->errors()->all() as $error) {
        $this->error($error);
      }

      return self::FAILURE;
    }

    try {
      DB::beginTransaction();

      $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'is_admin' => true,
        'email_verified_at' => now(),
      ]);

      DB::commit();

      $this->info('Staff account created successfully!');

      $this->table(['ID', 'Name', 'Email', 'Password'], [
        [$user->id, $user->name, $user->email, $password],
      ]);

      if ($this->option('send-email') || $this->confirm('Send welcome email?')) {
        $user->notify(new StaffAccountCreated($password));
        $this->info('Welcome email sent to ' . $email);
      }

      return self::SUCCESS;
    } catch (Throwable $e) {
      DB::rollBack();

      $this->error('Failed to create staff account: ' . $e->getMessage());

      return self::FAILURE;
    }
  }
}
```

### Step 5.2 — Verify file parses

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail php -l Modules/UserAuth/app/Console/CreateStaffAccount.php
```

Expected: No errors.

---

## PHASE 6 — Create `Modules/UserAuth/app/Notifications/StaffAccountCreated.php`

**Goal:** Create a queueable mail notification sent when `CreateStaffAccount` is run with `--send-email`.

### Step 6.1 — Create the notification file

Create `Modules/UserAuth/app/Notifications/StaffAccountCreated.php` with this exact content:

```php
<?php

namespace Modules\UserAuth\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StaffAccountCreated extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(private readonly string $password)
  {
    $this->afterCommit()->onQueue('high');
  }

  public function via(object $notifiable): array
  {
    return ['mail'];
  }

  public function toMail(User $staff): MailMessage
  {
    return (new MailMessage())
        ->success()
        ->subject('Staff Account Created')
        ->greeting('Hello ' . $staff->first_name . ',')
        ->line(new HtmlString(
            'A staff account has been created for you on ' . config('app.name') . '. '
            . 'Your password is: <b>' . e($this->password) . '</b>'
        ))
        ->line('You can log in to your dashboard using the button below.')
        ->action('Log In', route('auth.login'))
        ->line('For security reasons, please change your password after your first login.')
        ->salutation(config('app.name'));
  }
}
```

### Step 6.2 — Verify file parses

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail php -l Modules/UserAuth/app/Notifications/StaffAccountCreated.php
```

Expected: No errors.

---

## PHASE 7 — Register command in `UserAuthServiceProvider`

**Goal:** Register `CreateStaffAccount::class` so Laravel discovers it as an artisan command.

### Step 7.1 — Edit `Modules/UserAuth/app/Providers/UserAuthServiceProvider.php`

Find the `registerCommands()` method:

```php
protected function registerCommands(): void
{
  // $this->commands([]);
}
```

Replace the entire method body with:

```php
protected function registerCommands(): void
{
  $this->commands([
    \Modules\UserAuth\Console\CreateStaffAccount::class,
  ]);
}
```

### Step 7.2 — Verify command is discoverable

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail artisan list niogg:create-staff-account
```

Expected: Command description is shown. Example output:

```
niogg:create-staff-account
  Create a new staff account with admin privileges.
```

---

## PHASE 8 — Create `tests/Feature/Commands/CreateStaffAccountCommandTest.php`

**Goal:** Cover command happy path, email notification, duplicate email failure, invalid email failure, and interactive random password flow.

### Step 8.1 — Create the test file

Create `tests/Feature/Commands/CreateStaffAccountCommandTest.php` with this exact content:

```php
<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Notifications\StaffAccountCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateStaffAccountCommandTest extends TestCase
{
  use RefreshDatabase;

  public function test_creates_staff_account_with_all_options(): void
  {
    Notification::fake();

    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Test Staff',
      '--email' => 'staff@example.com',
      '--password' => 'secure-password-123',
    ])->assertSuccessful();

    $this->assertDatabaseHas('users', [
      'email' => 'staff@example.com',
      'name' => 'Test Staff',
      'is_admin' => true,
    ]);

    $user = User::where('email', 'staff@example.com')->first();
    $this->assertNotNull($user->email_verified_at);

    Notification::assertNothingSent();
  }

  public function test_sends_welcome_email_when_send_email_flag_is_passed(): void
  {
    Notification::fake();

    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Notified Staff',
      '--email' => 'notified@example.com',
      '--password' => 'secure-password-123',
      '--send-email' => true,
    ])->assertSuccessful();

    $user = User::where('email', 'notified@example.com')->first();

    Notification::assertSentTo(
      [$user],
      StaffAccountCreated::class
    );
  }

  public function test_fails_on_duplicate_email(): void
  {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Duplicate',
      '--email' => 'existing@example.com',
      '--password' => 'secure-password-123',
    ])->assertFailed();
  }

  public function test_fails_on_invalid_email_format(): void
  {
    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Bad Email',
      '--email' => 'not-an-email',
      '--password' => 'secure-password-123',
    ])->assertFailed();
  }

  public function test_generates_random_password_when_not_provided(): void
  {
    Notification::fake();

    $this->artisan('niogg:create-staff-account')
      ->expectsQuestion('Full name', 'Random Staff')
      ->expectsQuestion('Email address', 'random@example.com')
      ->expectsConfirmation('Use a random password?', 'yes')
      ->expectsConfirmation('Send welcome email?', 'no')
      ->assertSuccessful();

    $this->assertDatabaseHas('users', [
      'email' => 'random@example.com',
      'name' => 'Random Staff',
      'is_admin' => true,
    ]);
  }
}
```

### Step 8.2 — Run tests

```bash
vendor/bin/sail bin pint --dirty
vendor/bin/sail artisan test --compact --filter=CreateStaffAccountCommandTest
```

Expected: All 5 tests pass.

---

## PHASE 9 — Final verification pass

**Goal:** Ensure everything works together with no regressions.

### Step 9.1 — Run Pint

```bash
vendor/bin/sail bin pint --dirty
```

### Step 9.2 — Run all new tests

```bash
vendor/bin/sail artisan test --compact --filter=BaseModelTest
vendor/bin/sail artisan test --compact --filter=CreateStaffAccountCommandTest
vendor/bin/sail artisan test --compact --filter=AuthenticationTest
```

### Step 9.3 — Run all auth tests

```bash
vendor/bin/sail artisan test --compact tests/Feature/Auth/
```

### Step 9.4 — Run all unit tests

```bash
vendor/bin/sail artisan test --compact tests/Unit/
```

### Step 9.5 — Run full test suite

```bash
vendor/bin/sail artisan test --compact
```

### Step 9.6 — Verify command is functional

```bash
vendor/bin/sail artisan niogg:create-staff-account --help
```

Expected: Command shows help with all 4 options (email, password, name, send-email).

---

## Rollback

If any phase causes regressions:

```bash
git log --oneline -10   # identify phase commits
git revert <commit-hash>  # revert the specific phase
```

Each phase builds on the previous one, so reverting Phase X requires phases X through 9 to be reverted in reverse order.

---

## Unresolved Questions

1. **BaseModel `resolveRouteBinding` with `str_obfuscate`** — Skipped. If route model obfuscation (URL-friendly encrypted IDs) is desired later, port `str_obfuscate` from master's `app/helpers.php` and the `resolveRouteBinding` override from master's `BaseModel`.

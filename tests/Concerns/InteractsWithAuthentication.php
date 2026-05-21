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
      'is_admin' => FALSE,
      'is_super_admin' => FALSE,
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

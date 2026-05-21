<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  public function test_is_admin_returns_true_for_admin(): void
  {
    $user = User::factory()->admin()->create();
    $this->assertTrue($user->isAdmin());
  }

  public function test_is_admin_returns_true_for_super_admin(): void
  {
    $user = User::factory()->superAdmin()->create();
    $this->assertTrue($user->isAdmin());
  }

  public function test_is_admin_returns_false_for_regular_user(): void
  {
    $user = User::factory()->create(['is_admin' => FALSE, 'is_super_admin' => FALSE]);
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

  public function test_is_super_admin_returns_false_for_regular_user(): void
  {
    $user = User::factory()->create(['is_admin' => FALSE, 'is_super_admin' => FALSE]);
    $this->assertFalse($user->isSuperAdmin());
  }

  public function test_get_type_returns_super_admin(): void
  {
    $user = User::factory()->superAdmin()->create();
    $this->assertEquals('SuperAdmin', $user->getType());
  }

  public function test_get_type_returns_admin(): void
  {
    $user = User::factory()->admin()->create();
    $this->assertEquals('Admin', $user->getType());
  }

  public function test_get_type_returns_app_user(): void
  {
    $user = User::factory()->create(['is_admin' => FALSE, 'is_super_admin' => FALSE]);
    $this->assertEquals('AppUser', $user->getType());
  }
}

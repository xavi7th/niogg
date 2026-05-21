<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use App\Models\User;
use Spatie\ResponseCache\Facades\ResponseCache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HandleInertiaRequestsTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    ResponseCache::clear();
    $this->withoutMiddleware(\Spatie\ResponseCache\Middlewares\CacheResponse::class);
  }

  public function test_shared_app_props_are_included(): void
  {
    $response = $this->get('/');

    $response->assertInertia(
        fn ($page) => $page
            ->has('app')
    );
  }

  public function test_user_props_included_when_authenticated(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertInertia(
        fn ($page) => $page
            ->has('auth.user')
            ->where('auth.user.id', $user->id)
    );
  }

  public function test_user_props_null_when_guest(): void
  {
    $response = $this->get('/');

    $response->assertInertia(
        fn ($page) => $page
            ->has('auth')
            ->where('auth.user', NULL)
    );
  }
}

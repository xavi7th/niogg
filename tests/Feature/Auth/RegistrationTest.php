<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
  use RefreshDatabase;

  public function test_registration_screen_returns_not_found(): void
  {
    $response = $this->get('/register');

    $response->assertStatus(404);
  }

  public function test_registration_post_returns_not_found(): void
  {
    $response = $this->post('/register', [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password',
      'password_confirmation' => 'password',
    ]);

    $response->assertStatus(404);
  }
}

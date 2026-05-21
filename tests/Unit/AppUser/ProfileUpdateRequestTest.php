<?php

namespace Tests\Unit\AppUser;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AppUser\Http\Requests\ProfileUpdateRequest;

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

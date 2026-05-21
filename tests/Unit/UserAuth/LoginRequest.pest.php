<?php

use App\Models\User;
use Modules\UserAuth\Http\Requests\LoginRequest;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('authorize returns true for any user', function (): void {
    $user = User::factory()->create();
    $request = new LoginRequest();
    $request->setUserResolver(fn () => $user);

    expect($request->authorize())->toBeTrue();
});

test('validation requires email and password', function (): void {
    $response = $this->post(route('auth.login'), [
        'email' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});

test('validation requires valid email format', function (): void {
    $response = $this->post(route('auth.login'), [
        'email' => 'not-an-email',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('rate limiting blocks excessive attempts', function (): void {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post(route('auth.login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['email']);
    $response->assertSessionHasErrors('email', trans('auth.throttle'));
});

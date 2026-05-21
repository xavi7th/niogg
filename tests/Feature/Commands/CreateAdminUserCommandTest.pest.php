<?php

use App\Models\User;

test('creates admin user with all options', function (): void {
    $this->artisan('admin:create', [
        '--name' => 'Super Admin',
        '--email' => 'admin@example.com',
        '--password' => 'secure-password-123',
    ])->assertSuccessful();

    expect(User::where('email', 'admin@example.com')->exists())->toBeTrue();

    $user = User::where('email', 'admin@example.com')->first();
    expect($user->is_admin)->toBeTrue()
        ->and($user->is_super_admin)->toBeTrue();
});

test('fails on duplicate email', function (): void {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->artisan('admin:create', [
        '--name' => 'Duplicate',
        '--email' => 'existing@example.com',
        '--password' => 'secure-password-123',
    ])->assertFailed();
});

test('fails on invalid email format', function (): void {
    $this->artisan('admin:create', [
        '--name' => 'Bad Email',
        '--email' => 'not-an-email',
        '--password' => 'secure-password-123',
    ])->assertFailed();
});

test('fails on short password', function (): void {
    $this->artisan('admin:create', [
        '--name' => 'Short Password',
        '--email' => 'short@example.com',
        '--password' => 'short',
    ])->assertFailed();
});

test('creates admin interactively', function (): void {
    $this->artisan('admin:create')
        ->expectsQuestion('Name', 'Interactive Admin')
        ->expectsQuestion('Email address', 'interactive@example.com')
        ->expectsQuestion('Password', 'secure-password-123')
        ->expectsQuestion('Confirm password', 'secure-password-123')
        ->assertSuccessful();

    expect(User::where('email', 'interactive@example.com')->exists())->toBeTrue();
});

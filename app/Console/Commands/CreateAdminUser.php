<?php

namespace App\Console\Commands;

use Throwable;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
  protected $signature = 'admin:create {--name= : Admin name} {--email= : Admin email} {--password= : Admin password}';

  protected $description = 'Create a new admin user';

  public function handle(): int
  {
    $name = $this->option('name') ?? $this->ask('Name');
    $email = $this->option('email') ?? $this->ask('Email address');
    $password = $this->option('password') ?? $this->secret('Password');
    $passwordConfirmation = $this->option('password') ? $password : $this->secret('Confirm password');

    $validator = Validator::make([
      'name' => $name,
      'email' => $email,
      'password' => $password,
      'password_confirmation' => $passwordConfirmation,
    ], [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ], [
      'email.unique' => 'A user with this email already exists.',
      'password.min' => 'Password must be at least 8 characters.',
      'password.confirmed' => 'Passwords do not match.',
    ]);

    if ($validator->fails()) {
      foreach ($validator->errors()->all() as $error) {
        $this->error($error);
      }

      return self::FAILURE;
    }

    if ($password !== $passwordConfirmation) {
      $this->error('Passwords do not match.');

      return self::FAILURE;
    }

    try {
      DB::beginTransaction();

      $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'is_admin' => TRUE,
        'is_super_admin' => TRUE,
      ]);

      DB::commit();

      $this->info('Admin user created successfully.');
      $this->table(['ID', 'Name', 'Email', 'Admin', 'Super Admin'], [
        [$user->id, $user->name, $user->email, 'Yes', 'Yes'],
      ]);

      return self::SUCCESS;
    } catch (Throwable $e) {
      DB::rollBack();

      $this->error('Failed to create admin user: ' . $e->getMessage());

      return self::FAILURE;
    }
  }
}

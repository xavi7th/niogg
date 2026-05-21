<?php

namespace Modules\UserAuth\Console;

use Throwable;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
    } elseif ($this->confirm('Use a random password?', TRUE)) {
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
        'is_admin' => TRUE,
      ]);

      $user->email_verified_at = now();
      $user->save();

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

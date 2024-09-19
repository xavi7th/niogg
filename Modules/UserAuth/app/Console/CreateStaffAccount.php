<?php

namespace Modules\UserAuth\Console;

use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Modules\UserAuth\Notifications\StaffAccountCreated;

class CreateStaffAccount extends Command
{
  protected $signature = 'niogg:create-staff-account
                          {--email= : The email address of the new staff}
                          {--P|password= : The password of the new staff}
                          {--name= : The full name of the new staff}
                          {--send-email : Determines if we should send the new staff a mail notification}';

  protected $description = 'Create a new staff account.';

  public function handle()
  {
    try {
      $staff = $this->createAccount();
    } catch (QueryException $th) {
      $this->newLine(2);
      $this->error($th->getMessage());
      $this->newLine(1);

      return self::FAILURE;
    } catch (InvalidArgumentException $th) {
      $this->newLine(2);
      $this->error('Invalid email provided.');
      $this->newLine(1);

      return self::FAILURE;
    }

    $this->info('Staff created successfully! Password: ' . $this->getPassword());

    if ($this->option('send-email') || $this->confirm('Send email?')) {
      $staff->notify(new StaffAccountCreated($this->getPassword()));

      $this->info('Staff notified via email!');
    }

    return self::SUCCESS;
  }

  private function getPassword()
  {
    static $password = NULL;

    if ($password === NULL) {
      $password = $this->option('password');

      if ( ! $password && ! $this->confirm('Should we use a random password?')) {
        $password = $this->secret('Password');
      } else {
        $password = Str::random(8);
      }
    }

    return $password;
  }

  private function createAccount(): User|int
  {
    $staff = new User();
    $email = $this->getInput('email');

    $validator = Validator::make(data: ['email' => $email], rules: ['email' => ['required', 'email']]);

    if ($validator->fails()) {
      throw new InvalidArgumentException();
    }

    $staff->email = $email;
    $staff->name = $this->getInput('name');
    $staff->password = $this->getInput('password');
    $staff->email_verified_at = now();

    $staff->save();

    return $staff->refresh();
  }

  public function getInput(string $key): ?string
  {
    return match ($key) {
      'email' => $this->option('email') ?? $this->ask('Email'),
      'password' => bcrypt($this->getPassword()),
      'name' => $this->option('name') ?? $this->ask('Name'),
      default => NULL,
    };
  }
}

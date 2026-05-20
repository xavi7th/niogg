<?php

namespace Modules\AppUser\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  public function run(): void
  {
    if (User::where('email', 'staff@asukeglobal.com')->exists()) {
      return;
    }

    User::create([
      'name' => 'Admin',
      'email' => 'staff@asukeglobal.com',
      'password' => Hash::make('Loraine@17'),
      'is_admin' => TRUE,
      'is_super_admin' => TRUE,
      'email_verified_at' => now(),
    ]);
  }
}

<?php

namespace Modules\AppUser\Database\Seeders;

use Illuminate\Database\Seeder;

class AppUserDatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $this->call(AdminUserSeeder::class);
  }
}

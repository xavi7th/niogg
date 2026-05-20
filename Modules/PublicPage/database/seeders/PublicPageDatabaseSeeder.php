<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;

class PublicPageDatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $this->call(EventsTableSeeder::class);
    $this->call(EventCategorySeeder::class);
    $this->call(VideosTableSeeder::class);
    $this->call(EventPhotosTableSeeder::class);
  }
}

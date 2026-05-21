<?php

namespace Modules\Conference\Tests\Feature;

use Modules\Conference\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConferencePageTest extends TestCase
{
  use RefreshDatabase;

  public function test_conference_page_renders(): void
  {
    $response = $this->get(route('app.conferences.launch.index'));

    $response->assertStatus(200);
  }
}

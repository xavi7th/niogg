<?php

namespace Tests\Concerns;

use Illuminate\Testing\TestResponse;

trait InteractsWithInertia
{
  protected function assertInertiaComponent(TestResponse $response, string $component): void
  {
    $response->assertInertia(fn ($page) => $page->component($component));
  }

  protected function assertInertiaHasProp(TestResponse $response, string $prop): void
  {
    $response->assertInertia(fn ($page) => $page->has($prop));
  }

  protected function assertInertiaPropEquals(TestResponse $response, string $prop, mixed $value): void
  {
    $response->assertInertia(fn ($page) => $page->where($prop, $value));
  }
}

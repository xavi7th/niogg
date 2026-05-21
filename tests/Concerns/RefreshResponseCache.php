<?php

namespace Tests\Concerns;

use Spatie\ResponseCache\ResponseCache;

trait RefreshResponseCache
{
  protected function clearResponseCache(): void
  {
    app(ResponseCache::class)->clear();
  }

  protected function setUpResponseCache(): void
  {
    parent::setUp();
    $this->clearResponseCache();
  }
}

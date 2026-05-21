<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use ReflectionMethod;
use App\Models\BaseModel;
use BadMethodCallException;

class BaseModelTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    BaseModel::$withoutAppends = FALSE;
  }

  private function makeModel(): BaseModel
  {
    return new class extends BaseModel
    {
      protected $table = 'users';

      protected $appends = ['dummy'];

      protected function getDummyAttribute(): string
      {
        return 'dummy_value';
      }

      public static function method(): string
      {
        return 'cached_result';
      }
    };
  }

  private function invokeGetArrayableAppends(BaseModel $model): array
  {
    $reflection = new ReflectionMethod($model, 'getArrayableAppends');
    $reflection->setAccessible(TRUE);

    return $reflection->invoke($model);
  }

  public function test_where_like_applies_like_constraint(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->whereLike('name', 'john');

    $sql = $query->toSql();
    $bindings = $query->getBindings();

    $this->assertStringContainsString('LIKE', $sql);
    $this->assertContains('%john%', $bindings);
  }

  public function test_where_like_with_array_creates_or_where(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->whereLike(['name', 'email'], 'test');

    $sql = $query->toSql();

    $this->assertStringContainsString('OR', mb_strtoupper($sql));
  }

  public function test_where_like_with_null_columns_does_not_throw(): void
  {
    $model = $this->makeModel();

    $query = $model->newQuery()->whereLike(NULL, 'test');

    $sql = $query->toSql();

    $this->assertStringContainsString('select', $sql);
  }

  public function test_where_not_excludes_value(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->whereNot('is_admin', TRUE);

    $sql = $query->toSql();

    $this->assertStringContainsString('not', mb_strtolower($sql));
    $this->assertStringContainsString('is_admin', $sql);
  }

  public function test_or_where_not_adds_or_constraint(): void
  {
    $model = $this->makeModel();
    $query = $model->newQuery()->where('name', 'foo')->orWhereNot('is_admin', TRUE);

    $sql = $query->toSql();
    $lowerSql = mb_strtolower($sql);

    $this->assertStringContainsString('or', $lowerSql);
    $this->assertStringContainsString('not', $lowerSql);
  }

  public function test_without_appends_prevents_appended_attributes(): void
  {
    $model = $this->makeModel();

    $model->withoutAppends();
    $appends = $this->invokeGetArrayableAppends($model);

    $this->assertEmpty($appends);
  }

  public function test_get_arrayable_appends_returns_appends_by_default(): void
  {
    $model = $this->makeModel();

    $appends = $this->invokeGetArrayableAppends($model);

    $this->assertNotEmpty($appends);
    $this->assertContains('dummy', $appends);
  }

  public function test_call_static_resolves_get_cached_methods(): void
  {
    $model = $this->makeModel();
    $class = get_class($model);

    $result = $class::getCachedMethod();

    $this->assertEquals('cached_result', $result);
  }

  public function test_call_static_falls_through_to_parent_for_unknown_methods(): void
  {
    $model = $this->makeModel();
    $class = get_class($model);

    $this->expectException(BadMethodCallException::class);

    $class::nonExistentMethod();
  }

  public function test_without_appends_is_static_and_resets_per_calls(): void
  {
    $modelA = $this->makeModel();
    $modelB = $this->makeModel();

    $modelA->withoutAppends();
    $appendsA = $this->invokeGetArrayableAppends($modelA);
    $appendsB = $this->invokeGetArrayableAppends($modelB);

    $this->assertEmpty($appendsA, 'Model A should have no appends after withoutAppends()');
    $this->assertEmpty($appendsB, 'Model B is also affected because withoutAppends is a static flag');
  }
}

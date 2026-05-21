<?php

namespace App\Models;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BaseModel extends Model
{
  public static bool $withoutAppends = FALSE;

  /**
   * Scope a query to exclude records where column matches value.
   */
  public function scopeWhereNot(Builder $query, string $column, mixed $value = NULL): Builder
  {
    return $query->where($column, '!=', $value);
  }

  /**
   * Scope a query to OR-exclude records where column matches value.
   */
  public function scopeOrWhereNot(Builder $query, string|Closure $column, mixed $value = NULL): Builder
  {
    return $query->orWhere($column, '!=', $value);
  }

  /**
   * Scope a query to search multiple columns with LIKE.
   */
  public function scopeWhereLike(Builder $query, string|array|null $columns = NULL, string|int|null $search = NULL): Builder
  {
    return $query->where(function (Builder $query) use ($columns, $search): void {
      foreach (Arr::wrap($columns) as $col) {
        $query->orWhere($col, 'LIKE', '%' . $search . '%');
      }
    });
  }

  /**
   * Scope to suppress appended attributes on the next serialization.
   */
  public function scopeWithoutAppends(Builder $query): Builder
  {
    self::$withoutAppends = TRUE;

    return $query;
  }

  /**
   * Get the array of appendable attributes, respecting the withoutAppends flag.
   */
  protected function getArrayableAppends(): array
  {
    if (self::$withoutAppends) {
      return [];
    }

    return parent::getArrayableAppends();
  }

  /**
   * Handle dynamic static method calls — resolves getCached{Method}() patterns.
   */
  public static function __callStatic($method, $parameters)
  {
    if (str_starts_with($method, 'getCached') && method_exists(static::class, lcfirst(mb_substr($method, 9)))) {
      $instance = new static();

      return $instance->{lcfirst(mb_substr($method, 9))}(...$parameters);
    }

    return parent::__callStatic($method, $parameters);
  }
}

<?php

namespace App\Models;

use Closure;
use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BaseModel extends Model
{
  public static $withoutAppends = FALSE;

  public function scopeWhereNot($query, $column, $value = NULL): void
  {
    $query->where($column, $value, NULL, 'and not');
  }

  public function scopeOrWhereNot(Builder $query, string|Closure $column, mixed $value = NULL): void
  {
    $query->where($column, $value, NULL, 'or not');
  }

  public function scopeWhereLike(Builder $query, string|array|null $columns = NULL, string|int|null $search = NULL): void
  {
    $query->where(function ($query) use ($columns, $search): void {
      foreach (Arr::wrap($columns) as $col) {
        $query->orWhere($col, 'LIKE', '%' . $search . '%');
      }
    });
  }

  public function scopeWithoutAppends(Builder $query): Builder
  {
    self::$withoutAppends = TRUE;

    return $query;
  }

  protected function getArrayableAppends(): array
  {
    if (self::$withoutAppends) {
      return [];
    }

    return parent::getArrayableAppends();
  }

  /**
   * Retrieve the model for a route bound value.
   *
   * @param  mixed  $value
   * @param  string|null  $field
   */
  public function resolveRouteBinding($value, $field = NULL): ?static
  {
    $query_key = $field ?? $this->getKeyName();

    try {
      return $this->where($query_key, str_obfuscate($value, TRUE))->firstOrFail();
    } catch (Throwable $th) {
      return parent::resolveRouteBinding($value, $field);
    }
  }

  public static function __callStatic($method, $parameters)
  {
    if (method_exists(static::class, 'getCached' . ucfirst($method))) {
      return (new static())->{'getCached' . ucfirst($method)}(...$parameters);
    }

    return parent::__callStatic($method, $parameters);
  }
}

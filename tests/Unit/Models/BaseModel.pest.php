<?php

use ReflectionMethod;
use App\Models\BaseModel;

beforeEach(function (): void {
    BaseModel::$withoutAppends = FALSE;
});

function makeModel(): BaseModel
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

function invokeGetArrayableAppends(BaseModel $model): array
{
    $reflection = new ReflectionMethod($model, 'getArrayableAppends');
    $reflection->setAccessible(TRUE);

    return $reflection->invoke($model);
}

test('where like applies like constraint', function (): void {
    $model = makeModel();
    $query = $model->newQuery()->whereLike('name', 'john');

    $sql = $query->toSql();
    $bindings = $query->getBindings();

    expect($sql)->toContain('LIKE')
        ->and($bindings)->toContain('%john%');
});

test('where like with array creates or where', function (): void {
    $model = makeModel();
    $query = $model->newQuery()->whereLike(['name', 'email'], 'test');

    $sql = $query->toSql();

    expect(mb_strtoupper($sql))->toContain('OR');
});

test('where like with null columns does not throw', function (): void {
    $model = makeModel();

    $query = $model->newQuery()->whereLike(NULL, 'test');

    expect($query->toSql())->toContain('select');
});

test('where not excludes value', function (): void {
    $model = makeModel();
    $query = $model->newQuery()->whereNot('is_admin', TRUE);

    $sql = $query->toSql();

    expect(mb_strtolower($sql))->toContain('not')
        ->and($sql)->toContain('is_admin');
});

test('or where not adds or constraint', function (): void {
    $model = makeModel();
    $query = $model->newQuery()->where('name', 'foo')->orWhereNot('is_admin', TRUE);

    $sql = mb_strtolower($query->toSql());

    expect($sql)->toContain('or')
        ->and($sql)->toContain('not');
});

test('without appends prevents appended attributes', function (): void {
    $model = makeModel();
    $model->withoutAppends();
    $appends = invokeGetArrayableAppends($model);

    expect($appends)->toBeEmpty();
});

test('get arrayable appends returns appends by default', function (): void {
    $model = makeModel();
    $appends = invokeGetArrayableAppends($model);

    expect($appends)->not->toBeEmpty()
        ->and($appends)->toContain('dummy');
});

test('call static resolves get cached methods', function (): void {
    $model = makeModel();
    $class = get_class($model);

    $result = $class::getCachedMethod();

    expect($result)->toBe('cached_result');
});

test('call static falls through to parent for unknown methods', function (): void {
    $model = makeModel();
    $class = get_class($model);

    $class::nonExistentMethod();
})->throws(BadMethodCallException::class);

test('without appends is static flag affecting all instances', function (): void {
    $modelA = makeModel();
    $modelB = makeModel();

    $modelA->withoutAppends();
    $appendsA = invokeGetArrayableAppends($modelA);
    $appendsB = invokeGetArrayableAppends($modelB);

    expect($appendsA)->toBeEmpty('Model A should have no appends after withoutAppends()')
        ->and($appendsB)->toBeEmpty('Model B is also affected because withoutAppends is a static flag');
});

<?php

namespace Oooiik\LaravelQueryFilter\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Oooiik\LaravelQueryFilter\Filters\QueryFilter;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;

/**
 * @method static Builder filter(string $filter, array $validated = [])
 */
trait Filterable
{
    public function scopeFilter(Builder $query, $filter, array $validated = [])
    {
        if (!class_exists($filter)) {
            throw new ClassNotFoundError('Class not found', 500);
        }
        if (!is_subclass_of($filter, QueryFilter::class)) {
            throw new ClassNotFoundError('It is not a successor class of Filter', 500);
        }
        return $filter::builder($query)->apply($validated)->query();
    }

    /**
     * @param string $filter
     * @return QueryFilter
     */
    public static function createFilter($filter)
    {
        if (!class_exists($filter)) {
            throw new ClassNotFoundError('Class not found', 500);
        }
        if (!is_subclass_of($filter, QueryFilter::class)) {
            throw new ClassNotFoundError('It is not a successor class of Filter', 500);
        }
        $query = self::query();
        return $filter::builder($query);
    }
}

<?php

namespace Oooiik\LaravelQueryFilter\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Oooiik\LaravelQueryFilter\Filters\QueryFilter;
use Oooiik\LaravelQueryFilter\Services\FilterService;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;

/**
 * @method static Builder filter(array $validated = [])
 * @property string $defaultFilter;
 */
trait Filterable
{
//    protected $defaultFilter;

    public function scopeFilter(Builder $query, array $params = [])
    {
        if (!class_exists($this->defaultFilter)) {
            throw new ClassNotFoundError('Class not found', 500);
        }
        if (!is_subclass_of($this->defaultFilter, QueryFilter::class)) {
            throw new ClassNotFoundError('It is not a successor class of Filter', 500);
        }
        return (new FilterService( $this->defaultFilter, $query))->apply($params)->query();
    }

    /**
     * @param QueryFilter|string $filter
     * @return FilterService
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
        return new FilterService($filter, $query);
    }
}

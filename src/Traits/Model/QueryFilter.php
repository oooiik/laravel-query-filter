<?php

namespace Oooiik\LaravelQueryFilter\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder queryFilter(array $validated = [])
 */
trait QueryFilter
{
    public function scopeQueryFilter(Builder $query, array $validated = [])
    {
        $modelName = class_basename(static::class);
        $queryFilterClassName = '\\App\\Filters\\Query\\' . $modelName . 'QueryFilter';
        if (class_exists($queryFilterClassName)) {
            return $queryFilterClassName::builder($query)->apply($validated)->query();
        } else {
            return $query;
        }
    }
}

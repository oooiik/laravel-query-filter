<?php

namespace Oooiik\LaravelQueryFilter\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    /** @var Builder */
    protected $builder;
    /** @var Builder */
    protected $realBuilder;

    public function __construct(Builder $builder)
    {
        $this->realBuilder = $builder;
        $this->builder = clone $this->realBuilder;
    }

    public static function builder(Builder $builder)
    {
        return new static($builder);
    }

    public function filters()
    {
        $staticClassMethods = get_class_methods(static::class);
        $selfClassMethods = get_class_methods(self::class);
        return array_diff($staticClassMethods, $selfClassMethods);
    }

    public function apply(array $validated)
    {
        foreach ($this->filters() as $filter) {
            if (in_array($filter, array_keys($validated))) {
                $this->$filter($validated[$filter], $validated);
            }
        }
        return $this;
    }

    public function resetApply(array $validated)
    {
        $this->builder = clone $this->realBuilder;
        return $this->apply($validated);
    }

    public function query()
    {
        return $this->builder;
    }
}

<?php

namespace Oooiik\LaravelQueryFilter\Filters;

use http\Exception\BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    /** @var Builder */
    protected $builder;
    /** @var Builder */
    protected $realBuilder;

    public array $default = [];

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
        $validatedKeys = array_keys($validated);
        $defaultKeys = array_keys($this->default);
        foreach ($this->filters() as $filter) {
            if (in_array($filter, $validatedKeys)) {
                $this->$filter($validated[$filter], $validated);
            } elseif (in_array($filter, $defaultKeys)) {
                $this->$filter($this->default[$filter], $validated);
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

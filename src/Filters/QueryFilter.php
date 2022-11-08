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

    /**
     * Here, default parameters for functions are saved
     * @var array
     */
    public array $default = [];

    /**
     * Here, if a function does not work, a helper function is shown for it
     * @var array
     */
    public array $fallback = [];

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
        $fallbackKeys = array_keys($this->fallback);

        foreach ($this->filters() as $filter) {
            if (in_array($filter, $validatedKeys)) {
                $this->$filter($validated[$filter], $validated);
            } elseif (in_array($filter, $defaultKeys)) {
                $this->$filter($this->default[$filter], $validated);
            } elseif (in_array($filter, $fallbackKeys)) {
                if(!in_array($this->fallback[$filter], $this->filters())){
                    throw new \BadMethodCallException("This method not found!", 500);
                }
                $this->{$this->fallback[$filter]}(null, $validated);
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

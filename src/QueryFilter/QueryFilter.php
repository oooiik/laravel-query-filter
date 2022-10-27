<?php

namespace Oooiik\LaravelQueryFilter\QueryFilter;

use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    /** @var Builder */
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public static function builder(Builder $builder)
    {
        return new static($builder);
    }

    public function filters(){
        $staticClassMethods = get_class_methods(static::class);
        $selfClassMethods = get_class_methods(self::class);
        return array_diff($staticClassMethods, $selfClassMethods);
    }

    public function apply(array $validated){
        foreach ($this->filters() as $filter){
            if(in_array($filter, array_keys($validated))){
                $this->$filter($validated[$filter], $validated);
            }
        }
        return $this;
    }

    public function query()
    {
        return $this->builder;
    }
}

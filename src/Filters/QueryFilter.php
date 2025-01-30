<?php

namespace Oooiik\LaravelQueryFilter\Filters;

use Illuminate\Contracts\Database\Query\Builder;

abstract class QueryFilter
{
    /** @var Builder */
    public $builder;

    /**
     * Here, default parameters for functions are saved
     * @var array
     */
    public $default = [];

    /**
     * Here, if a function does not work, a helper function is shown for it
     * @var array
     */
    public $fallback = [];
}

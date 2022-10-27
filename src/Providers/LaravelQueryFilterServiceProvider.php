<?php

namespace Oooiik\LaravelQueryFilter\Providers;

use Oooiik\LaravelQueryFilter\Console\MakeFilter;

class LaravelQueryFilterServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFilter::class,
            ]);
        }
    }

}

<?php

namespace Oooiik\LaravelQueryFilter\Providers;

use Oooiik\LaravelQueryFilter\Console\MakeQueryFilter;

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
                MakeQueryFilter::class,
            ]);
        }
    }

}

<?php

namespace Oooiik\LaravelQueryFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class MakeQueryFilter extends Command
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;
    protected $modelName;

    protected $signature = 'make:query-filter {model}';

    protected $description = 'Create a new query filter class';

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $model = $this->argument('model');
        $this->setModelName($model);
        $this->buildClass();
    }


    protected function buildClass()
    {
        $stub = $this->replaceStub();
        if (is_file($this->getGenerationPath())) {
            throw new InvalidArgumentException('Such a class exists!');
        }
        $this->files->ensureDirectoryExists($this->getGenerationDir());
        $this->files->put($this->getGenerationPath(), $stub);
        $this->info('QueryFilter created successfully');
    }

    protected function getGenerationPath()
    {
        return app_path('Filters/Query/' . $this->getGenerationClassName() . '.php');
    }

    protected function getGenerationDir()
    {
        return app_path('Filters/Query');
    }

    protected function getGenerationClassName()
    {
        return $this->modelName . 'QueryFilter';
    }

    protected function hasModel($model)
    {
        return class_exists($model);
    }

    protected function setModelName($model)
    {
        $modelClass = '\\App\\Models\\' . $model;
        if (!$this->hasModel($modelClass)) {
            throw new InvalidArgumentException('Model not found!');
        }
        $this->modelName = class_basename($model);
    }

    protected function replaceStub()
    {
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$this->getNamespace(), $this->getClassName()],
            $this->getStub()
        );

        return $stub;
    }

    protected function getStub()
    {
        return $this->files->get(__DIR__ . '/stubs/query-filter.stub');
    }

    protected function getNamespace()
    {
        return 'App\Filters\Query';
    }

    protected function getClassName()
    {
        return $this->modelName . 'QueryFilter';
    }

}

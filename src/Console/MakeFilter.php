<?php

namespace Oooiik\LaravelQueryFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class MakeFilter extends Command
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /** @var string $className */
    protected $className;

    protected $signature = 'make:filter {name}';

    protected $description = 'Create a new query filter class';

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $this->setFilterName($name);
        $this->buildClass();
    }

    public function setFilterName($name){
        $class = '\\App\\Filters\\' . $name;
        if (class_exists($class)) {
            throw new InvalidArgumentException('Class exists!');
        }
        $this->className = $name;
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
        return app_path('Filters/' . $this->className . '.php');
    }

    protected function getGenerationDir()
    {
        return app_path('Filters');
    }

    protected function replaceStub()
    {
        return str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$this->getNamespace(), $this->className],
            $this->getStub()
        );
    }

    protected function getStub()
    {
        return $this->files->get(__DIR__ . '/stubs/filter.stub');
    }

    protected function getNamespace()
    {
        return 'App\Filters';
    }
}

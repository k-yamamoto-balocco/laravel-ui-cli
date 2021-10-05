<?php

namespace GitBalocco\LaravelUiCli\Command;

use Illuminate\Console\GeneratorCommand;

class MakeCliHandler extends GeneratorCommand
{
    /** @var string $name */
    protected $name = 'make:cli-handler';
    /** @var string $description */
    protected $description = 'Create a new CliHandler for CliCommand';
    /** @var string $type */
    protected $type = 'CliHandler';

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);
        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $relativePath = '/stubs/cli-handler.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
            ? $customPath
            : realpath(__DIR__ . '/../..' . $relativePath);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Console\Handlers';
    }
}

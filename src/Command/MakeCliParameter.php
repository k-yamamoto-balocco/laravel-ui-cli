<?php

namespace GitBalocco\LaravelUiCli\Command;

use Illuminate\Console\GeneratorCommand;

class MakeCliParameter extends GeneratorCommand
{
    /** @var string $name */
    protected $name = 'make:cli-parameter';
    /** @var string $description */
    protected $description = 'Create a new CliParameter(validation,handling variables) for CliCommand';
    /** @var string $type */
    protected $type = 'CliParameter';

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
        $relativePath = '/stubs/cli-parameter.stub';

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
        return $rootNamespace . '\Console\Parameters';
    }
}

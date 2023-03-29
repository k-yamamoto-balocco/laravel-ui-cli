<?php

namespace GitBalocco\LaravelUiCli\Command;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

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
        $stub = $this->replaceParameterClass($stub);
        $stub = $this->replaceUseParameterClass($stub);
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

    private function replaceParameterClass($stub)
    {
        if ($this->argument('parameter-class')) {
            $parameterClass = $this->myQualifyClassName($this->argument('parameter-class'));
        } else {
            $parameterClass = 'DefaultParameter';
        }
        $stub = str_replace(['{{ parameter-class }}'], $parameterClass, $stub);
        return $stub;
    }

    private function replaceUseParameterClass($stub)
    {
        if ($parameterClassName = $this->argument('parameter-class')) {
            $useParameterClassStatement = 'use ' . $this->myQualifyNamespace(
                    $parameterClassName,
                    '\\Console\\Parameters\\'
                ) . ';';
        } else {
            $useParameterClassStatement = 'use GitBalocco\LaravelUiCli\DefaultParameter;';
        }
        $stub = str_replace(['{{ use-parameter-class }}'], $useParameterClassStatement, $stub);
        return $stub;
    }

    private function myQualifyClassName($name)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);
        return array_slice(explode('\\', $name), '-1')[0];
    }
    private function myQualifyNamespace($name, $base)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);

        $namespace = '';
        $namespace .= trim($this->rootNamespace(), '\\');
        $namespace .= $base . $name;
        return $namespace;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command'],
            ['parameter-class', InputArgument::OPTIONAL, 'hoge'],
        ];
    }
}

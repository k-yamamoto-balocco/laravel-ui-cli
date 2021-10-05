<?php

namespace GitBalocco\LaravelUiCli\Command;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCliCommand extends GeneratorCommand
{
    /** @var string $name */
    protected $name = 'make:cli-command';
    /** @var string $description */
    protected $description = 'Create a new CliCommand (based on Artisan)';
    /** @var string $type */
    protected $type = 'CliCommand';

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

        $stub = str_replace(['dummy:command', '{{ command }}'], $this->option('command'), $stub);

        //なんとなくこの中でやっとくか・・・
        //parameter-class の
        $stub = $this->replaceParameterClass($stub);
        $stub = $this->replaceUseParameterClass($stub);
        $stub = $this->replaceHandlerClass($stub);
        $stub = $this->replaceUseHandlerClass($stub);

        return $stub;
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

    private function myQualifyClassName($name)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);
        return array_slice(explode('\\', $name), '-1')[0];
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

    private function myQualifyNamespace($name, $base)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);

        $namespace = '';
        $namespace .= trim($this->rootNamespace(), '\\');
        $namespace .= $base . $name;
        return $namespace;
    }

    private function replaceHandlerClass($stub)
    {
        if ($this->argument('handler-class')) {
            $handlerClass = $this->myQualifyClassName($this->argument('handler-class'));
        } else {
            $handlerClass = 'CliHandler';
        }
        $stub = str_replace(['{{ handler-class }}'], $handlerClass, $stub);
        return $stub;
    }

    private function replaceUseHandlerClass($stub)
    {
        if ($handlerClassName = $this->argument('handler-class')) {
            $useHandlerClassStatement = 'use ' . $this->myQualifyNamespace(
                $handlerClassName,
                '\\Console\\Handlers\\'
            ) . ';';
        } else {
            $useHandlerClassStatement = 'use GitBalocco\LaravelUiCli\CliHandler;';
        }
        $stub = str_replace(['{{ use-handler-class }}'], $useHandlerClassStatement, $stub);
        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $relativePath = '/stubs/cli-command.stub';
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
        return $rootNamespace . '\Console\Commands';
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
            ['handler-class', InputArgument::OPTIONAL, 'hoge']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'command',
                null,
                InputOption::VALUE_OPTIONAL,
                'The terminal command that should be assigned',
                'command:name'
            ],
        ];
    }
}

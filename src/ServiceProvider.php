<?php

namespace GitBalocco\LaravelUiCli;

use GitBalocco\LaravelUiCli\Command\MakeCliCommand;
use GitBalocco\LaravelUiCli\Command\MakeCliHandler;
use GitBalocco\LaravelUiCli\Command\MakeCliParameter;
use GitBalocco\LaravelUiCli\Command\MakeCliSet;
use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        if ($this->getMyApp()->runningInConsole()) {
            $this->commands($this->commandsToRegister());
        }

        $this->publishes($this->itemsToPublish());
    }

    protected function getMyApp()
    {
        return $this->app;
    }

    private function commandsToRegister(): array
    {
        return [
            MakeCliSet::class,
            MakeCliCommand::class,
            MakeCliParameter::class,
            MakeCliHandler::class,
        ];
    }

    /**
     * @return array
     * @psalm-suppress InvalidArrayOffset
     */
    private function itemsToPublish(): array
    {
        return [
            realpath(__DIR__ . '/../stubs/cli-command.stub') => base_path('stubs/cli-command.stub'),
            realpath(__DIR__ . '/../stubs/cli-handler.stub') => base_path('stubs/cli-handler.stub'),
            realpath(__DIR__ . '/../stubs/cli-parameter.stub') => base_path('stubs/cli-parameter.stub'),
        ];
    }
}

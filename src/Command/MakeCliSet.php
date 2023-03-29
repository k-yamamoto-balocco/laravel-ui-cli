<?php

namespace GitBalocco\LaravelUiCli\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeCliSet extends Command
{
    /** @var string $name */
    protected $signature = 'make:cli-set {name}';
    /** @var string $description */
    protected $description = 'Create a new CliCommand,CliHandler,CliParameters';

    public function handle()
    {
        $name = $this->argument('name');
        $parameterClass = $name . 'Parameter';
        $handlerClass = $name . 'Handler';

        Artisan::call('make:cli-handler', ['name' => $handlerClass,'parameter-class' => $parameterClass]);
        Artisan::call('make:cli-parameter', ['name' => $parameterClass]);
        Artisan::call(
            'make:cli-command',
            [
                'name' => $name . 'Command',
                'parameter-class' => $parameterClass,
                'handler-class' => $handlerClass
            ]
        );
    }
}

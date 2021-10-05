<?php

namespace GitBalocco\LaravelUiCli\Test\Feature\Command;

use Orchestra\Testbench\TestCase;

class Base extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [\GitBalocco\LaravelUiCli\ServiceProvider::class];
    }
}
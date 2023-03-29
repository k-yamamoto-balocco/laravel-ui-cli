<?php

namespace GitBalocco\LaravelUiCli;

use GitBalocco\LaravelUiCli\Contract\CliHandlerInterface;
use GitBalocco\LaravelUiCli\Contract\CliParameterInterface;

abstract class CliHandler implements CliHandlerInterface
{
    public function __construct(private CliParameterInterface $parameter)
    {
    }
}

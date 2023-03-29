<?php

namespace GitBalocco\LaravelUiCli;

use GitBalocco\LaravelUiCli\Contract\CliHandlerInterface;
use GitBalocco\LaravelUiCli\Contract\CliParameterInterface;

abstract class CliHandler implements CliHandlerInterface
{
    private int $exitStatus = 0;

    public function __construct(private CliParameterInterface $parameter)
    {
    }

    public function getExitStatus(): int
    {
        return $this->exitStatus;
    }

    protected function setExitStatus(int $exitStatus): void
    {
        $this->exitStatus = $exitStatus;
    }
}

<?php

namespace GitBalocco\LaravelUiCli\Contract;

interface CliHandlerInterface
{
    public function getExitStatus(): int;
}

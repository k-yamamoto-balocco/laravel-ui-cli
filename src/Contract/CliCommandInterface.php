<?php

namespace GitBalocco\LaravelUiCli\Contract;

interface CliCommandInterface
{
    public function handle(): int;
}

<?php

namespace GitBalocco\LaravelUiCli\Contract;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;

interface CliParameterInterface
{
    public function validator(): ValidatorContract;
}

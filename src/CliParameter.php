<?php

namespace GitBalocco\LaravelUiCli;

use GitBalocco\LaravelUiCli\Contract\CliParameterInterface;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;

abstract class CliParameter implements CliParameterInterface
{
    /** @var ValidatorContract|null $validator */
    private $validator;
    /** @var array $argumentsAndOptions */
    private $argumentsAndOptions;

    /**
     * CliParameter constructor.
     * @param array $argumentsAndOptions
     */
    public function __construct(array $argumentsAndOptions)
    {
        $this->argumentsAndOptions = $argumentsAndOptions;
    }

    /**
     * validator
     *
     * @return ValidatorContract
     */
    public function validator(): ValidatorContract
    {
        if (isset($this->validator)) {
            return $this->validator;
        }

        return $this->validator = Validator::make(
            $this->getArgumentsAndOptions(),
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }

    /**
     * @return array
     */
    protected function getArgumentsAndOptions(): array
    {
        return $this->argumentsAndOptions;
    }

    /**
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * messages
     *
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * attributes
     *
     * @return array
     */
    protected function attributes(): array
    {
        return [];
    }

    /**
     * 個々の実装を担当するCliParameterのサブクラスにおいてgetterを整備する際に利用することを意図した汎用getter。
     * パラメータ名文字列を直接記述する箇所がこのサブクラス内のみに限定されるようprotectedとしている。
     * また、外部からの呼び出しを想定していないため、意図しないパラメータ名文字列が与えられることも考慮しない。
     * （存在しないパラメータ名を指定した場合、例外が発生する。）
     * このget()を利用し、コマンドライン引数、オプションにアクセスするためのgetterを各サブクラスに実装すること。
     * @param string $key
     * @return mixed
     */
    protected function get(string $key)
    {
        $arguments = $this->getArgumentsAndOptions();
        return $arguments[$key];
    }
}

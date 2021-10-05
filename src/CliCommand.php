<?php

namespace GitBalocco\LaravelUiCli;

use GitBalocco\LaravelUiCli\Contract\CliCommandInterface;
use GitBalocco\LaravelUiCli\Contract\CliHandlerInterface;
use GitBalocco\LaravelUiCli\Contract\CliParameterInterface;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CliCommand
 */
abstract class CliCommand extends Command implements CliCommandInterface
{
    /** @var string $parameterClassName */
    protected $parameterClassName = DefaultParameter::class;
    /** @var CliParameterInterface $cliParameter */
    private $cliParameter;
    /** @var CliHandlerInterface $cliHandler */
    private $cliHandler;

    /**
     * CliCommand constructor.
     */
    final public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @psalm-suppress InvalidArgument
     */
    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        //CliParameterをセット
        $this->cliParameter = $this->createCliParameter($this->createDataToValidate());

        //バリデーションを実行
        $this->validateWithCliParameter();

        //Handlerをセット
        $this->cliHandler = $this->getLaravel()->call([$this, 'createCliHandler']);

        //コマンドクラスの初期化処理
        if ($this->initCliCommandMethodExists()) {
            $this->getLaravel()->call([$this, 'initCliCommand']);
        }
        //親クラスのexecuteを実行
        return $this->parentExecute($input, $output);
    }

    /**
     * @param mixed $argument
     * @return CliParameterInterface
     * @throws \Exception
     */
    protected function createCliParameter($argument): CliParameterInterface
    {
        if ($className = $this->getParameterClassName()) {
            if (!class_exists($className)) {
                throw new \Exception('');
            }
            $object = new $className($argument);
            if (!is_subclass_of($object, CliParameterInterface::class)) {
                throw new \Exception('');
            }
            return $object;
        }
        //DefaultParameterを作成
        return new DefaultParameter($argument);
    }

    /**
     * @return string
     */
    protected function getParameterClassName(): string
    {
        return $this->parameterClassName;
    }

    /**
     * createDataToValidate
     *
     * @return array
     */
    protected function createDataToValidate(): array
    {
        $data = array_merge($this->arguments(), $this->options());
        return array_filter(
            $data,
            function ($value) {
                return $value !== null;
            }
        );
    }

    /**
     *
     */
    private function validateWithCliParameter(): void
    {
        //バリデーション実行
        if ($this->getCliParameter()->validator()->fails()) {
            throw new InvalidArgumentException(
                $this->formatErrors($this->getCliParameter()->validator()->errors()->all())
            );
        }
    }

    /**
     * @return CliParameterInterface
     */
    protected function getCliParameter(): CliParameterInterface
    {
        return $this->cliParameter;
    }

    /**
     * @param array $errors
     * @return string
     */
    private function formatErrors(array $errors): string
    {
        return implode(PHP_EOL, $errors);
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    protected function initCliCommandMethodExists(): bool
    {
        //UnitTest実装の都合上protectedメソッドとしているが、このクラス以外から呼び出されることを想定していない
        //(本来privateメソッドとするべきである)
        assert(
            debug_backtrace()[1]['class'] === 'CliCommand',
            'CliCommand::parentExecute() called by ' . debug_backtrace()[1]['class']
        );
        return method_exists($this, 'initCliCommand');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @codeCoverageIgnore
     */
    protected function parentExecute(InputInterface $input, OutputInterface $output): int
    {
        //UnitTest実装の都合上protectedメソッドとしているが、このクラス以外から呼び出されることを想定していない
        //(本来privateメソッドとするべきである)
        assert(
            debug_backtrace()[1]['class'] === 'CliCommand',
            'CliCommand::parentExecute() called by ' . debug_backtrace()[1]['class']
        );
        return parent::execute($input, $output);
    }

    /**
     * @return CliHandlerInterface
     */
    protected function getCliHandler(): CliHandlerInterface
    {
        return $this->cliHandler;
    }
}

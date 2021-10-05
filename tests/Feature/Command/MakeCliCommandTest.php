<?php

namespace GitBalocco\LaravelUiCli\Test\Feature\Command;

use GitBalocco\LaravelUiCli\Command\MakeCliCommand;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\Command\MakeCliCommand
 * GitBalocco\LaravelUiCli\Tests\Feature\Command\MakeCliCommandTest
 */
class MakeCliCommandTest extends Base
{
    /** @var $testClassName as test target class name */
    protected $testClassName = MakeCliCommand::class;

    /**
     * @coversNothing
     */
    public function test_NoArgumentRaiseRuntimeException()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('make:cli-command');
    }

    /**
     */
    public function test_OnlyNameArgument()
    {
        $this->artisan('make:cli-command', ['name' => 'TestCliCommand1'])
            ->expectsOutput('CliCommand created successfully.')
            ->assertExitCode(0);

        //コマンド実行によりクラスファイルが作成される
        $this->assertFileExists(app_path('Console/Commands/TestCliCommand1.php'));

    }

    /**
     */
    public function test_WithAllArgument()
    {
        $this->artisan(
            'make:cli-command',
            [
                'name' => 'TestCliCommand2',
                'parameter-class' => 'DummyCliHandler',
                'handler-class' => 'DummyCliParameter'
            ]
        )
            ->expectsOutput('CliCommand created successfully.')
            ->assertExitCode(0);

        //コマンド実行によりクラスファイルが作成される
        $this->assertFileExists(app_path('Console/Commands/TestCliCommand2.php'));

    }

    protected function tearDown(): void
    {
        //テストケース実行時、コマンド成功するとファイルが作成され、他のテストに影響を与える可能性があるため削除する
        array_map('unlink', glob(app_path('Console/Commands/*.php')));
        parent::tearDown();
    }


}

<?php

namespace GitBalocco\LaravelUiCli\Tests\Feature\Command;

use GitBalocco\LaravelUiCli\Command\MakeCliParameter;
use GitBalocco\LaravelUiCli\Test\Feature\Command\Base;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\Command\MakeCliParameter
 * GitBalocco\LaravelUiCli\Tests\Feature\Command\MakeCliParameterTest
 */
class MakeCliParameterTest extends Base
{
    /** @var $testClassName as test target class name */
    protected $testClassName = MakeCliParameter::class;

    /**
     * @coversNothing
     */
    public function test_NoArgumentRaiseRuntimeException()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('make:cli-parameter');
    }

    /**
     *
     */
    public function test_WithNameArgument()
    {
        $this->artisan('make:cli-parameter', ['name' => 'TestCliParameter1'])
            ->expectsOutput('CliParameter created successfully.')
            ->assertExitCode(0)
        ;

        //コマンド実行によりクラスファイルが作成される
        $this->assertFileExists(app_path('Console/Parameters/TestCliParameter1.php'));

    }

    protected function tearDown(): void
    {
        //テストケース実行時、コマンド成功するとファイルが作成され、他のテストに影響を与える可能性があるため削除する
        array_map('unlink', glob(app_path('Console/Parameters/*.php')));
        parent::tearDown();
    }

}

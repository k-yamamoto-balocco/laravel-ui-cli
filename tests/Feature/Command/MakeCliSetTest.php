<?php

namespace GitBalocco\LaravelUiCli\Tests\Feature\Command;

use GitBalocco\LaravelUiCli\Command\MakeCliSet;
use GitBalocco\LaravelUiCli\Test\Feature\Command\Base;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\Command\MakeCliSet
 * GitBalocco\LaravelUiCli\Tests\Feature\Command\MakeCliSetTest
 */
class MakeCliSetTest extends Base
{
    /** @var $testClassName as test target class name */
    protected $testClassName = MakeCliSet::class;

    /**
     * @coversNothing
     */
    public function test_NoArgumentRaiseRuntimeException()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('make:cli-set');
    }

    /**
     *
     */
    public function test_WithNameArgument()
    {
        $this->artisan('make:cli-set', ['name' => 'TestCase1'])
            ->assertExitCode(0);

        //コマンド実行によりクラスファイルが作成される
        $this->assertFileExists(app_path('Console/Commands/TestCase1Command.php'));
        $this->assertFileExists(app_path('Console/Handlers/TestCase1Handler.php'));
        $this->assertFileExists(app_path('Console/Parameters/TestCase1Parameter.php'));
    }

    protected function tearDown(): void
    {
        //テストケース実行時、コマンド成功するとファイルが作成され、他のテストに影響を与える可能性があるため削除する
        array_map('unlink', glob(app_path('Console/Commands/*.php')));
        array_map('unlink', glob(app_path('Console/Handlers/*.php')));
        array_map('unlink', glob(app_path('Console/Parameters/*.php')));
        parent::tearDown();
    }

}

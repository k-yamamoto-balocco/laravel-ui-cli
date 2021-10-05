<?php

namespace GitBalocco\LaravelUiCli\Tests\Feature\Command;

use GitBalocco\LaravelUiCli\Command\MakeCliHandler;
use GitBalocco\LaravelUiCli\Test\Feature\Command\Base;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\Command\MakeCliHandler
 * GitBalocco\LaravelUiCli\Tests\Feature\Command\MakeCliHandlerTest
 */
class MakeCliHandlerTest extends Base
{
    /** @var $testClassName as test target class name */
    protected $testClassName = MakeCliHandler::class;

    /**
     * @coversNothing
     */
    public function test_NoArgumentRaiseRuntimeException(){
        $this->expectException(RuntimeException::class);
        $this->artisan('make:cli-handler');
    }

    /**
     */
    public function test_WithNameArgument(){
        $this->artisan('make:cli-handler',['name'=>'TestCliHandler1'])
            ->expectsOutput('CliHandler created successfully.')
            ->assertExitCode(0);

        //コマンド実行によりクラスファイルが作成される
        $this->assertFileExists(app_path('Console/Handlers/TestCliHandler1.php'));

    }

    protected function tearDown(): void
    {
        //テストケース実行時、コマンド成功するとファイルが作成され、他のテストに影響を与える可能性があるため削除する
        array_map('unlink', glob(app_path('Console/Handlers/*.php')));
        parent::tearDown();
    }
}

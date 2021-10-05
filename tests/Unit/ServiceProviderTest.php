<?php

namespace GitBalocco\LaravelUiCli\Tests\Unit;

use GitBalocco\LaravelUiCli\ServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\ServiceProvider
 * GitBalocco\LaravelUiCli\Tests\Unit\ServiceProviderTest
 */
class ServiceProviderTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ServiceProvider::class;

    /**
     * @covers ::boot
     * @covers ::commandsToRegister
     * @covers ::itemsToPublish
     */
    public function test_boot()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();

        $targetClass->shouldReceive('getMyApp->runningInConsole')->once()->andReturnTrue();
        $targetClass->shouldReceive('commands')->withAnyArgs()->once();
        $targetClass->shouldReceive('publishes')->withAnyArgs()->once();
        $targetClass->boot();
    }

    /**
     * @covers ::getMyApp
     */
    public function test_getMyApp()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $targetClass->app = 'app property';
                $actual = $targetClass->getMyApp();
                //assertions
                $this->assertSame('app property', $actual);

            },
            $this,
            $targetClass
        )->__invoke();
    }
}

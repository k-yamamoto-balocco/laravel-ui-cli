<?php

namespace GitBalocco\LaravelUiCli\Tests\Unit;

use GitBalocco\LaravelUiCli\DefaultParameter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\DefaultParameter
 * GitBalocco\LaravelUiCli\Tests\Unit\DefaultParameterTest
 */
class DefaultParameterTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = DefaultParameter::class;

    /**
     * @covers ::rules
     */
    public function test_rules()
    {
        $targetClass = new $this->testClassName(['opt-name' => 'opt-value']);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $actual = $targetClass->rules();
                //assertions
                $this->assertSame([], $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }
}

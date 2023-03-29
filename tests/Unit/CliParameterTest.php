<?php

namespace GitBalocco\LaravelUiCli\Tests\Unit;

use GitBalocco\LaravelUiCli\CliParameter;
use GitBalocco\LaravelUiCli\Contract\CliParameterInterface;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\CliParameter
 * GitBalocco\LaravelUiCli\Tests\Unit\CliParameterTest
 */
class CliParameterTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = CliParameter::class;

    /**
     * @covers ::messages
     */
    public function test_messages()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $actual = $targetClass->messages();
        //空の配列。実装上の必要にしたがってサブクラスがオーバーライドするべき内容である。
        $this->assertSame([], $actual);
    }

    /**
     * @covers ::attributes
     */
    public function test_attributes()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $actual = $targetClass->attributes();
        //空の配列。実装上の必要にしたがってサブクラスがオーバーライドするべき内容である。
        $this->assertSame([], $actual);
    }

    /**
     * @covers ::getArgumentsAndOptions
     */
    public function test_getArgumentsAndOptions()
    {
        $argumentArray = [
            'opt-name1' => 'opt-value1',
            'opt-name2' => 'opt-value2',
        ];

        $targetClass = \Mockery::mock($this->testClassName, [$argumentArray])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $actual = $targetClass->getArgumentsAndOptions();
        $this->assertSame($argumentArray, $actual);
        return $targetClass;
    }

    /**
     * @param mixed $targetClass
     * @covers ::get
     * @depends test_getArgumentsAndOptions
     */
    public function test_get($targetClass)
    {
        //テスト対象メソッドの実行1
        $actual = $targetClass->get('opt-name1');
        $this->assertSame('opt-value1', $actual);

        //テスト対象メソッドの実行2
        $actual = $targetClass->get('opt-name2');
        $this->assertSame('opt-value2', $actual);

        //テスト対象メソッドの実行3（存在しないパラメタ名を指定した場合、NULLが返却される）
        $actual = $targetClass->get('opt-name-not-exists');
        $this->assertNull($actual);
    }

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $argumentArray = [
            'opt' => 'value',
        ];

        $targetClass = \Mockery::mock($this->testClassName, [$argumentArray])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->assertInstanceOf(CliParameterInterface::class, $targetClass);
    }

    /**
     * @covers ::validator
     */
    public function test_validator()
    {
        $stubValidator = \Mockery::mock(ValidatorContract::class);
        $argumentArray = ['opt' => 'value',];
        $ruleArray = ['rule-key' => ['rule1', 'rule2']];
        $messageArray = ['message-key' => ['message1', 'message2']];
        $attributeArray = ['attr-key' => ['attr1', 'attr2']];

        $targetClass = \Mockery::mock($this->testClassName, [$argumentArray])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('rules')->withNoArgs()->once()->andReturn($ruleArray);
        $targetClass->shouldReceive('messages')->withNoArgs()->once()->andReturn($messageArray);
        $targetClass->shouldReceive('attributes')->withNoArgs()->once()->andReturn($attributeArray);

        Validator::shouldReceive('make')
            ->with(
                $argumentArray,
                $ruleArray,
                $messageArray,
                $attributeArray
            )->once()
            ->andReturn($stubValidator);
        Validator::makePartial();

        //テスト対象メソッドの実行(1回目)
        $actual = $targetClass->validator();
        $this->assertSame($stubValidator, $actual);

        //テスト対象メソッドの実行(2回目、2回叩いても、前回の結果を返すだけ！)
        $actual = $targetClass->validator();
        $this->assertSame($stubValidator, $actual);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}

<?php

namespace GitBalocco\LaravelUiCli\Tests\Unit;

use GitBalocco\LaravelUiCli\CliCommand;
use GitBalocco\LaravelUiCli\Contract\CliCommandInterface;
use GitBalocco\LaravelUiCli\Contract\CliHandlerInterface;
use GitBalocco\LaravelUiCli\Contract\CliParameterInterface;
use GitBalocco\LaravelUiCli\DefaultParameter;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @coversDefaultClass \GitBalocco\LaravelUiCli\CliCommand
 * GitBalocco\LaravelUiCli\Tests\Unit\CliCommandTest
 */
class CliCommandTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = CliCommand::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $targetClass = \Mockery::mock($this->testClassName);
        $this->assertInstanceOf(CliCommandInterface::class, $targetClass);
        $this->assertInstanceOf(Command::class, $targetClass);
    }

    /**
     * @covers ::createCliParameter
     */
    public function test_createCliParameter_Default()
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getParameterClassName')->once()->andReturn('');
        $actual = $targetClass->createCliParameter([]);
        $this->assertInstanceOf(DefaultParameter::class, $actual);
    }

    /**
     * @covers ::createCliParameter
     */
    public function test_createCliParameter()
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $stubParameter = new class() implements CliParameterInterface {
            public function validator(): ValidatorContract
            {
                return Validator::make([]);
            }
        };
        $targetClass->shouldReceive('getParameterClassName')->once()->andReturn(get_class($stubParameter));
        $actual = $targetClass->createCliParameter([]);
        $this->assertInstanceOf(get_class($stubParameter), $actual);
    }


    /**
     * @covers ::createDataToValidate
     * @dataProvider createDataToValidateDataProvider
     */
    public function test_createDataToValidate(array $arguments, array $options, array $expect)
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('arguments')->once()->andReturn($arguments);
        $targetClass->shouldReceive('options')->once()->andReturn($options);
        $actual = $targetClass->createDataToValidate();
        $this->assertSame($expect, $actual);
    }

    public function createDataToValidateDataProvider()
    {
        return [
            //TESTCASE-01
            ['引数、オプションともに空の場合' => [], [], []],
            //TESTCASE-02
            ['値がNULLの項目は削除される' => ['key1' => null], ['key2' => null], []],
            //TESTCASE-03
            [
                'argumentsとoptionはoptionの値優先' =>
                    ['key' => 'argument-value'], //arguments()
                ['key' => 'option-value'], //options()
                ['key' => 'option-value']// 結果
            ],
            //TESTCASE-04
            [
                'argumentsとoptionがマージされる' =>
                    ['arg-key1' => 'argument-key1-value'], //arguments()
                ['opt-key1' => 'option-key1-value'], //options()
                ['arg-key1' => 'argument-key1-value', 'opt-key1' => 'option-key1-value']// 結果
            ],
        ];
    }

    /**
     * @covers ::execute
     */
    public function test_execute_正常系DoesntHaveInitMethod()
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $stubInput = \Mockery::mock(InputInterface::class);
        $stubOutput = \Mockery::mock(OutputInterface::class);

        $stubParameter = new class() implements CliParameterInterface {
            public function validator(): ValidatorContract
            {
                return Validator::make([], []);
            }
        };
        $stubHandler = \Mockery::mock(CliHandlerInterface::class);

        $targetClass->shouldReceive('createDataToValidate')->once()->andReturn([]);
        $targetClass->shouldReceive('createCliParameter')->once()->andReturn($stubParameter);
        $targetClass->shouldReceive('getLaravel->call')
            ->with([$targetClass, 'createCliHandler'])
            ->once()
            ->andReturn($stubHandler);
        $targetClass->shouldReceive('initCliCommandMethodExists')->once()->andReturn(false);

        //最後に、親クラスのexecuteに処理を引き渡す
        $targetClass->shouldReceive('parentExecute')
            ->with($stubInput, $stubOutput)
            ->once()
            ->andReturn(0);

        //assertion
        $actual = $targetClass->execute($stubInput, $stubOutput);
        $this->assertSame(0, $actual);

        return [$targetClass, $stubHandler, $stubParameter];
    }

    /**
     * @covers ::execute
     * @covers ::validateWithCliParameter
     * @covers ::formatErrors
     */
    public function test_execute_Validationエラーによる例外()
    {
        $targetClass = \Mockery::mock($this->testClassName)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $stubInput = \Mockery::mock(InputInterface::class);
        $stubOutput = \Mockery::mock(OutputInterface::class);

        $stubParameter = new class() implements CliParameterInterface {
            public function validator(): ValidatorContract
            {
                return Validator::make([], []);
            }
        };
        //バリデータに渡す検査対象配列
        $targetClass->shouldReceive('createDataToValidate')->once()->andReturn([]);
        $targetClass->shouldReceive('createCliParameter')->once()->andReturn($stubParameter);
        //バリデーションでエラーが発生したテイで
        $targetClass->shouldReceive('getCliParameter->validator->fails')->andReturnTrue();
        $targetClass->shouldReceive('getCliParameter->validator->errors->all')->andReturn([]);

        $this->expectException(InvalidArgumentException::class);
        $targetClass->execute($stubInput, $stubOutput);
    }

    /**
     * @covers ::execute
     */
    public function test_execute_正常系HasInitMethod()
    {
        $targetClass = \Mockery::mock($this->testClassName)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $stubInput = \Mockery::mock(InputInterface::class);
        $stubOutput = \Mockery::mock(OutputInterface::class);

        $stubParameter = new class() implements CliParameterInterface {
            public function validator(): ValidatorContract
            {
                return Validator::make([], []);
            }
        };

        $targetClass->shouldReceive('createDataToValidate')->once()->andReturn([]);
        $targetClass->shouldReceive('createCliParameter')->once()->andReturn($stubParameter);
        $targetClass->shouldReceive('getLaravel->call')->with([$targetClass, 'createCliHandler'])->once();
        $targetClass->shouldReceive('initCliCommandMethodExists')->once()->andReturn(true);
        $targetClass->shouldReceive('getLaravel->call')->with([$targetClass, 'initCliCommand'])->once();

        //最後に、親クラスのexecuteに処理を引き渡す
        $targetClass->shouldReceive('parentExecute')
            ->with($stubInput, $stubOutput)
            ->once()
            ->andReturn(0);

        //assertion
        $actual = $targetClass->execute($stubInput, $stubOutput);
        $this->assertSame(0, $actual);
    }

    /**
     * execute() が正常に実行された後にのみgetCliHandler() が利用できる
     * @param mixed $depends
     * @covers ::getCliHandler
     * @depends test_execute_正常系DoesntHaveInitMethod
     */
    public function test_getCliHandler($depends)
    {
        $targetClass = $depends[0];
        $stubHandler = $depends[1];
        $actual = $targetClass->getCliHandler();
        $this->assertSame($stubHandler, $actual);
    }

    /**
     * execute() が正常に実行された後にのみgetCliParameter() が利用できる
     * @param mixed $depends
     * @covers ::getCliParameter
     * @depends test_execute_正常系DoesntHaveInitMethod
     */
    public function test_getCliParameter($depends)
    {
        $targetClass = $depends[0];
        $stubParameter = $depends[2];
        $actual = $targetClass->getCliParameter();
        $this->assertSame($stubParameter, $actual);
    }

    /**
     *
     * @covers ::getParameterClassName
     * @depends test___construct
     */
    public function test_getParameterClassName()
    {
        $targetClass = \Mockery::mock($this->testClassName)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $this->assertSame(DefaultParameter::class, $targetClass->getParameterClassName());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}


# laravel-ui-cli
## このパッケージが提供する機能
- LaravelのArtisan Commandを開発する際の雛形となる3つの抽象クラス

- 抽象クラスを継承した具象クラスファイルを作成する、4つのartisan make コマンド




## Artisan Commandを開発する際の雛形となる抽象クラス
このパッケージは、Artisanコマンドの実装を、責務を明確にした3種類のコンポーネントに分割することを意図して設計されています。

3つのコンポーネント、CliCommand、CliParameter、CliHandlerに対応し、同名の抽象クラス3つがこのパッケージに含まれています。

| コンポーネント名 | このパッケージが提供する抽象クラス | 責務                                                         |
| ---------------- | ---------------------------------- | ------------------------------------------------------------ |
| CliCommand       | GitBalocco\LaravelUiCli\CliCommand    | CLIからの入力を制御しCliParameter、CliHandlerに渡す<br />CliHandlerの実行結果をCLIに出力する |
| CliParameter     | GitBalocco\LaravelUiCli\CliParameter  | Cliからの入力に対するバリデーション<br />バリデートされた値をCliHandlerへ受け渡す際の窓口 |
| CliHandler       | GitBalocco\LaravelUiCli\CliHandler    | CLI入出力以外の処理。<br />原則としてUseCaseの呼び出し。     |

以下に、CliCommand、CliHandler、CliParameter、各々のコンポーネント/抽象クラスについて詳述します。


### CliCommand

コマンドの本体です。責務はCliに対する入出力の制御と、CliParameter、CliHandlerとの関係の定義です。（WebUIにおけるControllerとViewの役割を担当するイメージ）

#### 実装サンプル

各アプリケーションにおいてArtisan Commandを開発する場合に、CliCommandを継承した具象クラスを作成して、実装を行ってください。以下に、具象クラスの実装サンプルを示します。

```php ConcreteExampleCommand.php
<?php

namespace App\Console\Commands;

use App\Console\Handlers\ConcreteExampleHandler;
use App\Console\Parameters\ConcreteExampleParameter;
use GitBalocco\LaravelUiCli\CliCommand;
use GitBalocco\LaravelUiCli\Contract\CliCommandInterface;
use GitBalocco\LaravelUiCli\Contract\CliHandlerInterface;

/**
 * Class ConcreteExampleCommand
 * @package App\Console\Commands
 * @method ConcreteExampleParameter getCliParameter()
 * @method ConcreteExampleHandler getCliHandler()
 */
class ConcreteExampleCommand extends CliCommand implements CliCommandInterface
{
    /** @var string $signature The name and signature of the console command. */
    protected $signature = 'command:example';

    /** @var string $description The console command description. */
    protected $description = 'Command description';

    /** @var string $parameterClassName CliParameterInterfaceを実装した、引数の管理を担当するクラス名。*/
    protected $parameterClassName = ConcreteExampleParameter::class;

    /**
     * createCliHandler
     * @return CliHandlerInterface
     */
    public function createCliHandler(): CliHandlerInterface
    {
        return new ConcreteExampleHandler();
    }

    /**
     * initCliCommand
     * @return void
     */
    public function initCliCommand(): void
    {
    }

    /**
     * handle
     * @return int
     */
    public function handle(): int
    {
        $handler = $this->getCliHandler();
        return 0;
    }
}

```

具象クラスが持つpublicメソッドは、createCliHandler()、initCliCommand()、handle()の3種類のみ、としてください。

（private/protected メソッドは自由に増やして構いません）



具象クラスにおける実装上のポイントについて、以下に詳述します。

#### プロパティ $parameterClassName 

$parameterClassNameには、このコマンドの引数に対するバリデーションを担当するCliParameterクラスのクラス名を設定します。このプロパティに設定されたクラスはコマンド実行時に自動的にインスタンスとして生成され、バリデーションを行います。このプロパティに設定される値は、CliParameterInterfaceを実装しているクラス名でなければなりません。  

CliCommandがどのようにCliParameterのインスタンスを生成するかは、CliCommand::createCliParameter()、CliCommand::createDataToValidate() により定義されています。動作の詳細を知りたい場合は、各々のメソッドの実装内容を確認してください。  

CliParameterのインスタンス生成処理を変更したい場合、2つのメソッドをオーバーライドし、各具象クラスでの処理を変更してください。



#### メソッド createCliHandler()

CliHandlerの依存関係解決、およびインスタンス生成を担当するメソッドです。各具象クラスにおいて必ず実装する必要があります。

引数でメソッドインジェクションが可能となっており、このメソッド内で、CliHandlerの依存関係を解決してインスタンスを作成する処理を実装してください。

createCliHandler() メソッドの中で、コマンドライン引数、オプションにアクセスする必要がある場合、getCliParameter() 経由でCliParameterを利用することを推奨します。

※$this->argument() $this->option() なども当然利用可能ですが、[CliParameterに専用のゲッタ](#入力値に対する専用のgetter)を用意することを推奨しています。



#### メソッド initCliCommand()

CliCommandの初期化処理を担当するメソッドです。このメソッドの実装は任意です。初期化処理が不要な場合、このメソッドは削除して構いません。

引数でメソッドインジェクションが可能となっており、このメソッド内でCliCommandの依存解決や、プロパティの初期値設定等、各具象クラスの初期化にあたるコードを実装してください。


※__construct() は、オーバーライド不可としています。コンストラクタは、コマンドの実行が実際に行われる場合の他に、php artisan list 時にも実行されるため、handle()の実行に必要な初期化をコンストラクタと分けて定義する、という意図によるものです。



#### メソッド handle()

コマンド実装の本体です。各具象クラスにおいて必ず実装する必要があります。

Cliとの入出力に関する処理を記述します。入出力以外の処理内容は すべてCliHandlerに移譲しましょう。


※通常、handle()メソッドではメソッドインジェクションが可能ですが、CliCommandクラスを継承している具象クラスでは、メソッドインジェクションを記述することができません。（CliCommandInterfaceのシグネチャが引数0と定義されているため）したがって、CliCommandの実行に必要な依存関係の解決は initCliCommand() において行うよう実装してください。



### CliParameter

コマンド実行時引数、コマンド実行時オプションのバリデーションと、入力値に対するアクセス（getter）を担当します。

#### バリデーション

CliParameterは、LaravelのFormRequestとよく似た構造になっており、rules()、messages()、attributes()の3つのメソッドを実装することによって、入出力に対するバリデーションを簡単に実装することができるよう設計されています。

#### 入力値に対する専用のgetter

CliParameterの具象クラスに、引数、オプションにアクセスするための個別のgetterを実装してください。

- 例：コマンド実行時引数 name に対するgetterとして、getName() を実装する。
- 例：コマンド実行時オプション force に対するgetterとして、isForce() を実装する。

このような個別のゲッタを実装する際に利用可能な、汎用のprotected なゲッタ get() を用意してあります。各アプリケーション実装上の必要に応じて、get()を利用した個別のgetterを実装してください。



#### 実装サンプル

以下に、CliParameterを継承した具象クラスの実装サンプルを示します。

``` php ConcreteExampleParameter.php
<?php

namespace App\Console\Parameters;

use GitBalocco\LaravelUiCli\CliParameter;
use GitBalocco\LaravelUiCli\Contract\CliParameterInterface;

/**
 * Class ConcreteExampleParameter
 * @package App\Console\Parameters
 */
class ConcreteExampleParameter extends CliParameter implements CliParameterInterface
{
    /**
     * コマンドライン引数 'name' 専用のゲッタ
     * ※リテラルでの引数名指定はCliParameter内のみに限定しておくのが良い
     * @return string
     */
    public function getName(): string
    {
        //汎用ゲッタを利用して実装することが可能
        return (string)$this->get('name');
    }

    /**
     * コマンドラインオプション 'force' 専用のゲッタ
     * ※リテラルでのオプション名指定はCliParameter内のみに限定しておくのが良い
     * @return bool
     */
    public function isForce(): bool
    {
        //汎用ゲッタを利用して実装することが可能
        return (bool)$this->get('force');
    }

    /**
     * rules
     * @return array
     */
    protected function rules(): array
    {
        //以下に、コマンドライン引数、オプションに対するバリデーションルールを定義してください。
        return [
            'name'=>[
                'string','min:10'
            ]
        ];
    }

    /**
     * messages
     * @return array
     */
    protected function messages(): array
    {
        //以下に、バリデーションルールに対して適用するカスタムメッセージを定義してください。
        //メッセージのカスタマイズが不要な場合、このメソッドは削除して構いません。
        return [];
    }

    /**
     * attributes
     * @return array
     */
    protected function attributes(): array
    {
        //バリデーション属性のカスタマイズを行いたい場合に実装してください。
        //不要な場合、このメソッドは削除して構いません。
        return [];
    }
}

```

### CliHandler

コマンドの実装のうち、入出力以外の処理を担当します。

CliHandler自体は、特別な機能を有していません。CliCommandが入出力を、CliHandlerが入出力以外を担当する、という責務を明確にする目的のために用意されています。CliHandlerの具象クラスは、個々のアプリケーションの仕様に従って自由に実装を行ってください。

## 具象クラスファイルを作成するコマンド

前述の3つのコンポーネントに対して、具象クラスファイルを作成するコマンドを4種類提供します。

| sigunature         | 内容                                                         | stub                                                         | 作成されるファイル名                                         |
| ------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| make:cli-command   | CliCommandクラスを作成する。                                 | cli-command.stub                                             | App\Console\Commands\{name}.php                              |
| make:cli-parameter | CliParameterクラスを作成する。                               | cli-parameter.stub                                           | App\Console\Parameters\{name}.php                            |
| make:cli-handler   | CliHandelrクラスを作成する。                                 | cli-handler.stub                                             | App\Console\Handelrs\{name}.php                              |
| make:cli-set       | CliCommand<br />CliParameter<br />CliHandelrクラスのセットを作成する。 | cli-command.stub<br />cli-parameter.stub<br />cli-handler.stub | App\Console\Commands\\{name}Command.php<br />App\Console\Parameters\\{name}Parameter.php<br />App\Console\Handler\\{name}Handler.php |

これらのうち、実際の開発で頻繁に利用するべきなのは、make:cli-set です。

このコマンドは、3種類のコンポーネントに対応する具象クラスファイル3点を、それぞれの関係が定義された状態で適切なディレクトリに生成します。

### stubs

コマンド実行により作成されるクラスファイルの内容をカスタマイズしたい場合、stubsディレクトリ以下のstubファイルを編集してください。

デフォルトのstubを各アプリケーションのstubsディレクトリにコピーするには、以下のコマンドを実行します。

~~~ sh
php artisan vendor:publish --provider=GitBalocco\\LaravelUiCli\\ServiceProvider
~~~


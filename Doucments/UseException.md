# CommonException 使用指南

## 概述

`CommonException` 是一个通用的业务异常类,用于在应用程序中表示预期的业务逻辑错误。抛出异常时返回标准格式的数组:

```php
[
    'code' => 错误码,
    'error' => 错误标识,
    'msg' => 错误信息
]
```

## 基本使用

### 1. 抛出异常

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

// 抛出异常
throw new CommonException('WechatOfficialConfigNotSet');
```

自动返回:
```php
[
    'code' => 50000,
    'error' => 'WechatOfficialConfigNotSet',
    'msg' => '微信配置未设置'
]
```

### 2. 捕获异常

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

try {
    throw new CommonException('WechatOfficialConfigNotSet');
} catch (CommonException $e) {
    // 返回标准格式数组
    return $e->getErrorResponse();
}
```

### 3. 获取错误信息

```php
try {
    throw new CommonException('WechatOfficialConfigNotSet');
} catch (CommonException $e) {
    // 获取错误标识
    $errorKey = $e->getErrorKey();

    // 获取格式化的错误信息(用于返回给客户端)
    $response = $e->getErrorResponse();

    // 获取详细的错误信息(用于日志记录)
    $details = $e->getErrorDetails();
}
```

## 配置文件

### 1. 错误码配置文件位置

默认配置文件位于: `src/config/error-codes.php`

配置文件格式:
```php
return [
    // ========== 通用错误码 (10000-19999) ==========
    'ServerError' => [
        'code' => 10000,
        'error' => 'ServerError',
        'msg' => '服务器异常'
    ],
    'CodeError' => [
        'code' => 100010,
        'error' => 'CodeError',
        'msg' => '错误码不存在'
    ],
];
```

### 2. 错误码分类建议

| 错误码范围 | 说明 |
|-----------|------|
| 10000-19999 | 通用错误 |
| 20000-29999 | 用户相关 |
| 30000-39999 | 文章相关 |
| 40000-49999 | 订单相关 |
| 50000-59999 | 微信相关 |
| 60000-69999 | 支付相关 |

## 动态注册错误码

### 1. 注册单个模块的错误码

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

// 注册文章相关错误码
CommonException::registerErrorMapping([
    // 文章相关
    'GetArticleError' => [
        'code' => 30010,
        'error' => 'GetArticleError',
        'msg' => '获取文章失败'
    ],
    'AddArticleError' => [
        'code' => 30020,
        'error' => 'AddArticleError',
        'msg' => '添加文章(主)失败'
    ],
]);

// 使用自定义错误码
throw new CommonException('GetArticleError');
```

### 2. 替换所有错误码

```php
// 使用 $merge = false 参数替换所有错误码
CommonException::registerErrorMapping($mapping, false);
```

### 3. 从文件加载错误码

```php
// 加载自定义配置文件
$customErrorCodes = require __DIR__ . '/config/my-error-codes.php';
CommonException::registerErrorMapping($customErrorCodes);
```

## 自定义配置文件

### 1. 设置自定义配置文件路径

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

// 指向你的自定义配置文件
CommonException::setConfigFile('/path/to/your/error-codes.php');

// 然后就可以使用配置文件中定义的错误码了
throw new CommonException('YourCustomError');
```

### 2. 在 Laravel 项目中集成

```php
// App\Providers\AppServiceProvider.php
use YouHuJun\Tool\App\Exceptions\CommonException;

public function boot()
{
    // 加载项目中的错误码配置
    $errorCodes = require config_path('error-codes.php');
    CommonException::registerErrorMapping($errorCodes);
}
```

## 在 Laravel 中的使用

### 1. 自动捕获异常并返回 JSON

```php
// App\Exceptions\Handler.php
use YouHuJun\Tool\App\Exceptions\CommonException;

public function render($request, Throwable $exception)
{
    // 捕获组件包的 CommonException
    if ($exception instanceof CommonException) {
        return response()->json($exception->getErrorResponse(), 200);
    }

    return parent::render($request, $exception);
}
```

### 2. 在 Controller 中使用

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

class ArticleController extends Controller
{
    public function getArticle($id)
    {
        $article = Article::find($id);

        if (!$article) {
            throw new CommonException('GetArticleError');
        }

        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $article
        ]);
    }

    public function addArticle(Request $request)
    {
        try {
            // 业务逻辑
            throw new CommonException('AddArticleError');
        } catch (CommonException $e) {
            return response()->json($e->getErrorResponse(), 200);
        }
    }
}
```

### 3. 在 Service 中使用

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

class ArticleService
{
    public function getArticle($id)
    {
        $article = Article::find($id);

        if (!$article) {
            throw new CommonException('GetArticleError');
        }

        return $article;
    }
}
```

## API 方法说明

### 构造函数

```php
__construct(?string $errorKey = null, \Throwable $previous = null)
```

**参数:**
- `$errorKey` - 错误标识,如果在配置文件中存在,自动加载对应的 code 和 msg
- `$previous` - 先前的异常

### 实例方法

#### getErrorKey()
获取错误标识

```php
$errorKey = $e->getErrorKey();
// 返回: 'WechatOfficialConfigNotSet'
```

#### getErrorResponse()
获取格式化的错误信息(用于返回给客户端)

```php
$response = $e->getErrorResponse();
// 返回: ['code' => 50000, 'error' => 'WechatOfficialConfigNotSet', 'msg' => '微信配置未设置']
```

#### getErrorDetails()
获取详细的错误信息(用于日志记录)

```php
$details = $e->getErrorDetails();
// 返回: [
//     'code' => 50000,
//     'error' => 'WechatOfficialConfigNotSet',
//     'message' => '微信配置未设置',
//     'file' => '/path/to/file.php',
//     'line' => 123
// ]
```

### 静态方法

#### setConfigFile()
设置配置文件路径

```php
CommonException::setConfigFile('/path/to/your/error-codes.php');
```

#### registerErrorMapping()
注册自定义错误码

```php
CommonException::registerErrorMapping([
    'CustomError' => [
        'code' => 60001,
        'error' => 'CustomError',
        'msg' => '自定义错误'
    ]
]);
```

#### getErrorMapping()
获取所有错误码映射

```php
$allErrorCodes = CommonException::getErrorMapping();
```

## 最佳实践

### 1. 错误码命名规范

建议使用模块+操作的格式:

```
模块名 + 操作 + Error

示例:
- GetArticleError - 获取文章错误
- AddArticleError - 添加文章错误
- UpdateArticleError - 更新文章错误
- DeleteArticleError - 删除文章错误
- WechatOfficialGetAccessTokenError - 微信获取令牌错误
```

### 2. 错误码范围分配

| 模块 | 错误码范围 |
|------|-----------|
| 通用 | 10000-19999 |
| 用户 | 20000-29999 |
| 文章 | 30000-39999 |
| 订单 | 40000-49999 |
| 微信 | 50000-59999 |
| 支付 | 60000-69999 |
| 自定义 | 70000+ |

### 3. 在 Laravel ServiceProvider 中统一加载

```php
// App\Providers\AppServiceProvider.php
use YouHuJun\Tool\App\Exceptions\CommonException;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 加载项目错误码
        CommonException::registerErrorMapping([
            // 用户相关
            'UserNotFound' => [
                'code' => 20010,
                'error' => 'UserNotFound',
                'msg' => '用户不存在'
            ],
            // 文章相关
            'ArticleNotFound' => [
                'code' => 30010,
                'error' => 'ArticleNotFound',
                'msg' => '文章不存在'
            ],
        ]);
    }
}
```

### 4. 与 tool-helper.php 的 code() 函数配合

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

try {
    throw new CommonException('WechatOfficialConfigNotSet');
} catch (CommonException $e) {
    // 使用 code() 函数格式化
    return code($e->getErrorResponse());
}
```

## 常见问题

### Q: 错误码不存在会发生什么?

A: 如果传入的 `$errorKey` 在配置文件中不存在,会返回默认的 `CodeError`:

```php
[
    'code' => 10010,
    'error' => 'CodeError',
    'msg' => '错误码不存在'
]
```

### Q: 如何清空已注册的错误码?

A: 使用 `$merge = false` 参数:

```php
CommonException::registerErrorMapping([], false);
```

### Q: 配置文件只会加载一次吗?

A: 是的,配置文件采用懒加载机制,只在首次使用时加载一次。使用 `setConfigFile()` 后会重置加载状态。

## 完整示例

### Service 层示例

```php
namespace App\Services;

use YouHuJun\Tool\App\Exceptions\CommonException;

class WechatService
{
    public function getAuthUrl($config, $redirectUrl)
    {
        if (empty($config['appid'])) {
            throw new CommonException('WechatOfficialAppidRequired');
        }

        if (empty($config['appsecret'])) {
            throw new CommonException('WechatOfficialAppsecretRequired');
        }

        // 业务逻辑...
    }
}
```

### Controller 层示例

```php
namespace App\Http\Controllers;

use App\Services\WechatService;
use YouHuJun\Tool\App\Exceptions\CommonException;

class WechatController extends Controller
{
    protected $wechatService;

    public function __construct(WechatService $wechatService)
    {
        $this->wechatService = $wechatService;
    }

    public function getAuthUrl(Request $request)
    {
        try {
            $url = $this->wechatService->getAuthUrl($config, $redirectUrl);

            return response()->json([
                'code' => 0,
                'msg' => 'success',
                'data' => ['url' => $url]
            ]);
        } catch (CommonException $e) {
            return response()->json($e->getErrorResponse(), 200);
        }
    }
}
```

### Exception Handler 示例

```php
namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use YouHuJun\Tool\App\Exceptions\CommonException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // 捕获 CommonException 并返回标准格式
        if ($exception instanceof CommonException) {
            return response()->json($exception->getErrorResponse(), 200);
        }

        return parent::render($request, $exception);
    }
}
```

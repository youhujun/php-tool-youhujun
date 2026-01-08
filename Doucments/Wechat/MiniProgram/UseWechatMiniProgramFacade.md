# 微信小程序登录服务使用文档

## 概述

`WechatMiniProgramFacade` 是微信小程序登录服务的静态门面类,提供了通过code获取用户openid和session_key的功能。使用静态代理模式调用,简单便捷。

## 配置参数

### 1. `$params` - 登录参数

```php
$params = [
    'code' => '小程序登录code',           // 必填: 小程序登录code
    'appid' => 'YOUR_WECHAT_MINI_PROGRAM_APPID',       // 必填: 小程序AppId
    // 'anonymousCode' => '匿名code',       // 可选: 匿名code(与code二选一)
];
```

### 2. `$config` - 配置参数

```php
$config = [
    'appsecret' => 'your_appsecret',    // 必填: 小程序AppSecret
];
```

## 使用示例

### 基本用法 - 获取用户信息

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\MiniProgram\WechatMiniProgramFacade;

// 登录参数
$params = [
    'code' => '0e3q2k0004L9O90g0000000000',
    'appid' => 'YOUR_WECHAT_MINI_PROGRAM_APPID',
];

// 配置
$config = [
    'appsecret' => 'YOUR_WECHAT_MINI_PROGRAM_SECRET',
];

// 获取用户信息(返回数组)
$result = WechatMiniProgramFacade::getOpenIdByCode($params, $config);

// 响应数据结构:
// [
//     'response' => [
//         'openid' => 'ok-X068nPIY-emwpdlYAmoaef2h8',
//         'session_key' => 'CJsCJOkD3mFX8weh1ijyBQ==',
//         'unionid' => 'o7hrD6aYhmgFFy_qb238EOfKCS8I', // 如果用户绑定过开放平台
//     ],
//     'appid' => 'wx1234567890abcdef'
// ]

$openid = $result['response']['openid'];
$sessionKey = $result['response']['session_key'];

echo "用户OpenID: " . $openid . "\n";
echo "Session Key: " . $sessionKey . "\n";
```

### 使用集合对象格式返回

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\MiniProgram\WechatMiniProgramFacade;

$params = [
    'code' => '0e3q2k0004L9O90g0000000000',
    'appid' => 'YOUR_WECHAT_MINI_PROGRAM_APPID',
];

$config = [
    'appsecret' => 'YOUR_WECHAT_MINI_PROGRAM_SECRET',
];

// 获取用户信息(返回对象)
$result = WechatMiniProgramFacade::getOpenIdByCodeWithCollection($params, $config);

echo "OpenID: " . $result->response->openid . "\n";
echo "AppID: " . $result->appid . "\n";
```

### 完整的登录流程

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\MiniProgram\WechatMiniProgramFacade;
use YouHuJun\Tool\App\Exceptions\CommonException;

// 从小程序前端获取的code
$code = $_POST['code'] ?? '';

try {
    // 调用微信接口获取用户信息
    $result = WechatMiniProgramFacade::getOpenIdByCode([
        'code' => $code,
        'appid' => 'YOUR_WECHAT_MINI_PROGRAM_APPID',
    ], [
        'appsecret' => 'YOUR_WECHAT_MINI_PROGRAM_SECRET',
    ]);

    $openid = $result['response']['openid'];
    $sessionKey = $result['response']['session_key'];
    $unionid = $result['response']['unionid'] ?? null;

    // 在这里处理登录逻辑:
    // 1. 检查openid是否已存在
    // 2. 如果不存在,创建新用户
    // 3. 如果存在,更新用户信息
    // 4. 生成登录token返回给前端

    echo json_encode([
        'code' => 0,
        'message' => '登录成功',
        'data' => [
            'openid' => $openid,
            'token' => 'generated_token_here',
        ],
    ]);
} catch (CommonException $e) {
    echo json_encode([
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ]);
}
```

### 使用匿名code

```php
$config = [
    'appsecret' => 'your_appsecret_here',
];

// 对于某些场景,可能使用匿名code
$params = [
    'anonymousCode' => 'anonymous_code_here',
    'appid' => 'YOUR_WECHAT_MINI_PROGRAM_APPID',
];

$result = WechatMiniProgramFacade::getOpenIdByCode($params, $config);
```

## 返回值

### `getOpenIdByCode()` 返回数组格式:

```php
[
    'response' => [
        'openid' => 'ok-X068nPIY-emwpdlYAmoaef2h8',    // 用户唯一标识
        'session_key' => 'CJsCJOkD3mFX8weh1ijyBQ==',     // 会话密钥(用于解密敏感数据)
        'unionid' => 'o7hrD6aYhmgFFy_qb238EOfKCS8I', // 开放平台唯一标识(可选)
    ],
    'appid' => 'YOUR_WECHAT_MINI_PROGRAM_APPID',                    // 小程序AppId
]
```

### `getOpenIdByCodeWithCollection()` 返回对象格式:

```php
(object)[
    'response' => (object)[
        'openid' => 'ok-X068nPIY-emwpdlYAmoaef2h8',
        'session_key' => 'CJsCJOkD3mFX8weh1ijyBQ==',
        'unionid' => 'o7hrD6aYhmgFFy_qb238EOfKCS8I',
    ],
    'appid' => 'YOUR_WECHAT_MINI_PROGRAM_APPID',
]
```

## 错误处理

服务会抛出 `CommonException`,包含以下错误码:

| 错误码 | Code | 说明 |
|--------|------|------|
| ParamsIsNullError | 10020 | 参数为空(缺少code或anonymousCode) |
| AppidIsNullError | 10020 | AppId为空 |
| WechatMiniProgramSecretRequired | 52000 | 微信小程序AppSecret未设置 |
| WechatMiniProgramLoginError | 52010 | 微信小程序登录失败(微信接口返回错误) |

### 错误处理示例

```php
use YouHuJun\Tool\App\Exceptions\CommonException;
use YouHuJun\Tool\App\Facade\V1\Wechat\MiniProgram\WechatMiniProgramFacade;

try {
    $result = WechatMiniProgramFacade::getOpenIdByCode($params, $config);
    echo "登录成功,OpenID: " . $result['response']['openid'];
} catch (CommonException $e) {
    echo '错误码: ' . $e->getCode() . PHP_EOL;
    echo '错误信息: ' . $e->getMessage() . PHP_EOL;
}
```

## 注意事项

1. **code有效期**: 微信小程序的code有效期只有5分钟,且只能使用一次
2. **session_key安全**: session_key是敏感信息,不应存储或传递给前端,仅用于服务端解密数据
3. **unionid获取**: 只有用户在小程序和公众号/开放平台都绑定过才能获取到unionid
4. **AppSecret安全**: AppSecret不应暴露在前端,只应在服务端使用
5. **静态调用**: 使用 `WechatMiniProgramFacade::getOpenIdByCode()` 静态方法调用,无需实例化
6. **接口限制**: 微信接口有调用频率限制,请合理使用
7. **匿名code**: code和anonymousCode必须提供其中一个

## 微信接口说明

### 请求URL

```
https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
```

### 请求参数

| 参数 | 必填 | 说明 |
|------|------|------|
| appid | 是 | 小程序AppId |
| secret | 是 | 小程序AppSecret |
| js_code | 是 | 登录时获取的code |
| grant_type | 是 | 填写authorization_code |

### 返回参数

| 参数 | 说明 |
|------|------|
| openid | 用户唯一标识 |
| session_key | 会话密钥 |
| unionid | 用户在开放平台的唯一标识符(满足一定条件才返回) |
| errcode | 错误码 |
| errmsg | 错误信息 |

## 相关链接

- 微信小程序登录文档: https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-info/code2Session.html
- 微信开放平台: https://open.weixin.qq.com/
- Session Key说明: https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/signature.html

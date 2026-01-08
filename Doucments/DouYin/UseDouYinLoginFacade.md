# 抖音登录服务使用文档

## 概述

抖音登录服务提供了抖音小游戏和抖音小程序的登录功能,通过 code 换取 openid 和 session_key。

## 安装

```php
use YouHuJun\Tool\App\Facade\V1\DouYin\Login\DouYinLoginFacade;
```

## 功能方法

### 1. 抖音小游戏登录 - `getOpenIdByCodeWithMiniGame`

通过 code 获取抖音小游戏用户的 openid 和 session_key。

#### 方法签名

```php
DouYinLoginFacade::getOpenIdByCodeWithMiniGame(array $params, array $config): array
```

#### 参数说明

**$params 参数**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| code | string | 与 anonymousCode 二选一 | 登录凭证 |
| anonymousCode | string | 与 code 二选一 | 匿名登录凭证 |
| appid | string | 是 | 抖音小游戏 AppID |

**$config 配置**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| appsecret | string | 是 | 抖音小游戏 AppSecret |

#### 返回值

返回包含响应数据和 appid 的数组:

```php
[
    'response' => [
        'openid' => '用户openid',
        'session_key' => '会话密钥',
        'anonymous_openid' => '匿名openid',
        'unionid' => 'unionid',
        'error' => 0
    ],
    'appid' => '小游戏appid'
]
```

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\DouYin\Login\DouYinLoginFacade;

// 参数配置
$params = [
    'code' => '登录凭证',
    'appid' => 'YOUR_DOUYIN_MINIGAME_APPID'
];

$config = [
    'appsecret' => 'YOUR_DOUYIN_SECRET'
];

try {
    // 调用登录接口
    $result = DouYinLoginFacade::getOpenIdByCodeWithMiniGame($params, $config);
    
    // 获取 openid
    $openid = $result['response']['openid'];
    $sessionKey = $result['response']['session_key'];
    $unionid = $result['response']['unionid'];
    
    echo "登录成功!";
    echo "OpenID: " . $openid;
    echo "UnionID: " . $unionid;
    
} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "登录失败: " . $e->getMessage();
}
```

---

### 2. 抖音小程序登录 - `getOpenIdByCodeWithMiniProgram`

通过 code 获取抖音小程序用户的 openid 和 session_key。

#### 方法签名

```php
DouYinLoginFacade::getOpenIdByCodeWithMiniProgram(array $params, array $config): array
```

#### 参数说明

**$params 参数**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| code | string | 与 anonymousCode 二选一 | 登录凭证 |
| anonymousCode | string | 与 code 二选一 | 匿名登录凭证 |
| appid | string | 是 | 抖音小程序 AppID |

**$config 配置**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| appsecret | string | 是 | 抖音小程序 AppSecret |

#### 返回值

返回包含响应数据和 appid 的数组:

```php
[
    'response' => [
        'err_no' => 0,
        'err_tips' => 'success',
        'data' => [
            'openid' => '用户openid',
            'session_key' => '会话密钥',
            'anonymous_openid' => '匿名openid',
            'unionid' => 'unionid',
            'dopenid' => 'dopenid'
        ]
    ],
    'appid' => '小程序appid'
]
```

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\DouYin\Login\DouYinLoginFacade;

// 参数配置
$params = [
    'code' => '登录凭证',
    'appid' => 'YOUR_DOUYIN_MINIGAME_APPID'
];

$config = [
    'appsecret' => 'YOUR_DOUYIN_SECRET'
];

try {
    // 调用登录接口
    $result = DouYinLoginFacade::getOpenIdByCodeWithMiniProgram($params, $config);
    
    // 获取 openid
    $openid = $result['response']['data']['openid'];
    $sessionKey = $result['response']['data']['session_key'];
    $unionid = $result['response']['data']['unionid'];
    
    echo "登录成功!";
    echo "OpenID: " . $openid;
    echo "UnionID: " . $unionid;
    
} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "登录失败: " . $e->getMessage();
}
```

---

## 错误处理

所有方法在失败时都会抛出 `CommonException` 异常,建议使用 try-catch 捕获。

### 常见错误码

| 错误码 | 错误信息 | 说明 |
|--------|----------|------|
| 10020 | ParamsIsNullError | 参数为空,缺少 code 或 anonymousCode |
| 10020 | AppidIsNullError | appid 参数缺失 |
| 53000 | DouYinMiniGameSecretRequired | 抖音小游戏 AppSecret 未设置 |
| 53010 | DouYinMiniGameLoginError | 抖音小游戏登录失败 |
| 53020 | DouYinMiniProgramSecretRequired | 抖音小程序 AppSecret 未设置 |
| 53030 | DouYinMiniProgramLoginError | 抖音小程序登录失败 |

### 错误处理示例

```php
use YouHuJun\Tool\App\Facade\V1\DouYin\Login\DouYinLoginFacade;

try {
    $result = DouYinLoginFacade::getOpenIdByCodeWithMiniProgram($params, $config);
    // 处理登录成功逻辑
} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    // 获取错误信息
    $errorCode = $e->getCode();
    $errorMsg = $e->getMessage();
    
    // 根据错误码进行不同处理
    switch ($errorCode) {
        case 10020:
            // 参数错误
            break;
        case 53020:
            // AppSecret 配置错误
            break;
        case 53030:
            // 登录失败,可能是 code 过期或无效
            break;
        default:
            // 其他错误
            break;
    }
}
```

---

## 注意事项

1. **安全存储**: AppSecret 是敏感信息,请妥善保管,不要硬编码在前端代码中
2. **Code 时效性**: code 5分钟内有效,且只能使用一次
3. **SessionKey**: session_key 用于数据解密,不要传输到前端
4. **UnionID**: 只有绑定到开放平台的应用才能获取 unionid
5. **匿名登录**: anonymousCode 用于匿名用户场景,可获取匿名 openid

---

## 完整示例

```php
<?php

use YouHuJun\Tool\App\Facade\V1\DouYin\Login\DouYinLoginFacade;

/**
 * 抖音小程序登录处理
 */
function handleDouyinMiniProgramLogin($code, $appid, $appsecret)
{
    // 准备参数
    $params = [
        'code' => $code,
        'appid' => $appid
    ];
    
    $config = [
        'appsecret' => $appsecret
    ];
    
    try {
        // 调用登录接口
        $result = DouYinLoginFacade::getOpenIdByCodeWithMiniProgram($params, $config);
        
        // 提取用户信息
        $openid = $result['response']['data']['openid'];
        $sessionKey = $result['response']['data']['session_key'];
        $unionid = $result['response']['data']['unionid'] ?? null;
        $anonymousOpenid = $result['response']['data']['anonymous_openid'];
        
        // 在此处保存用户信息到数据库
        // ...
        
        // 返回给前端的用户标识(不包含敏感信息)
        return [
            'openid' => $openid,
            'unionid' => $unionid,
            'has_account' => false // 根据数据库查询结果设置
        ];
        
    } catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
        // 记录错误日志
        error_log("抖音小程序登录失败: " . $e->getMessage());
        
        // 返回错误信息
        return [
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ];
    }
}

// 使用示例
$loginResult = handleDouyinMiniProgramLogin(
    'user_code_here',
    'YOUR_DOUYIN_MINIPROGRAM_APPID',
    'YOUR_DOUYIN_SECRET'
);

if (isset($loginResult['error'])) {
    echo "登录失败: " . $loginResult['error'];
} else {
    echo "登录成功! OpenID: " . $loginResult['openid'];
}
```

---

## 对比表格

| 功能 | 抖音小游戏 | 抖音小程序 |
|------|-----------|-----------|
| 方法 | `getOpenIdByCodeWithMiniGame` | `getOpenIdByCodeWithMiniProgram` |
| API 地址 | minigame.zijieapi.com | developer.toutiao.com |
| 请求方式 | GET | POST |
| 返回结构 | 平铺结构 | data 嵌套结构 |
| 错误码字段 | error | err_no |
| OpenID 字段 | openid | data.openid |

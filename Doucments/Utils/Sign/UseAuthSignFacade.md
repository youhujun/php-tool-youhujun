# 认证签名服务使用文档

## 概述

认证签名服务提供了 HMAC-SHA256 签名生成功能,用于API接口认证、数据完整性校验等场景。

## 安装

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;
```

## 功能方法

### 1. 生成签名 - `makeSign`

使用 HMAC-SHA256 算法生成签名,支持参数排序和自定义密钥。

#### 方法签名

```php
AuthSignFacade::makeSign($params, $secretKey): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| params | array | 是 | 待签名的参数数组 |
| secretKey | string | 是 | 密钥 |

#### 签名规则

1. **参数排序**: 对参数按键名(升序)排序
2. **构建查询字符串**: 使用 `http_build_query()` 构建
3. **URL解码**: 对查询字符串进行URL解码
4. **拼接密钥**: 在字符串末尾拼接 `&secretKey=密钥`
5. **生成签名**: 使用 HMAC-SHA256 算法生成签名

#### 返回值

返回生成的签名字符串(64位十六进制字符串)。

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;

// 示例1: 基础签名
$params = [
    'user_id' => 123,
    'timestamp' => time(),
    'action' => 'login'
];
$secretKey = 'your-secret-key-123';

$sign = AuthSignFacade::makeSign($params, $secretKey);
echo "生成的签名: " . $sign . PHP_EOL;

// 示例2: 复杂参数签名
$params = [
    'api_key' => 'test-api-key',
    'timestamp' => 1673935200,
    'nonce' => 'abc123',
    'user_id' => 456,
    'action' => 'update_profile',
    'data' => json_encode(['name' => '张三', 'age' => 25])
];
$secretKey = 'app-secret-key-2024';

$sign = AuthSignFacade::makeSign($params, $secretKey);
echo "复杂参数签名: " . $sign . PHP_EOL;

// 示例3: 空参数签名
$params = [];
$secretKey = 'empty-secret-key';

$sign = AuthSignFacade::makeSign($params, $secretKey);
echo "空参数签名: " . $sign . PHP_EOL;
```

---

## 完整示例

### 示例1: API接口认证

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;

/**
 * 构建API请求参数并签名
 */
function buildAPIRequest(array $params, string $apiKey, string $apiSecret): array
{
    // 添加公共参数
    $params['api_key'] = $apiKey;
    $params['timestamp'] = time();
    $params['nonce'] = bin2hex(random_bytes(8));

    // 生成签名
    $sign = AuthSignFacade::makeSign($params, $apiSecret);

    // 添加签名到参数
    $params['sign'] = $sign;

    return $params;
}

/**
 * 验证API请求签名
 */
function verifyAPIRequest(array $params, string $apiSecret): bool
{
    // 获取请求签名
    $requestSign = $params['sign'] ?? '';

    // 移除签名参数
    unset($params['sign']);

    // 重新生成签名
    $generatedSign = AuthSignFacade::makeSign($params, $apiSecret);

    // 使用 hash_equals 防止时序攻击
    return hash_equals($generatedSign, $requestSign);
}

// 使用示例
$apiKey = 'test-api-key';
$apiSecret = 'app-secret-key-123456';

// 构建请求
$request = buildAPIRequest([
    'user_id' => 123,
    'action' => 'get_user_info'
], $apiKey, $apiSecret);

echo "API请求参数: " . json_encode($request, JSON_UNESCAPED_UNICODE) . PHP_EOL;

// 验证签名
$isValid = verifyAPIRequest($request, $apiSecret);
echo "签名验证结果: " . ($isValid ? '成功' : '失败') . PHP_EOL;
```

### 示例2: Webhook签名验证

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;

/**
 * 接收并验证Webhook请求
 */
function verifyWebhookRequest(array $requestBody, string $webhookSecret): bool
{
    // 获取请求签名
    $requestSign = $requestBody['sign'] ?? '';

    // 移除签名参数
    unset($requestBody['sign']);

    // 重新生成签名
    $generatedSign = AuthSignFacade::makeSign($requestBody, $webhookSecret);

    // 验证签名
    return hash_equals($generatedSign, $requestSign);
}

/**
 * 处理Webhook回调
 */
function handleWebhook(array $requestBody): void
{
    $webhookSecret = 'webhook-secret-key-2024';

    // 验证签名
    if (!verifyWebhookRequest($requestBody, $webhookSecret)) {
        http_response_code(401);
        echo "签名验证失败";
        return;
    }

    // 签名验证通过,处理业务逻辑
    $eventType = $requestBody['event_type'] ?? '';
    $data = $requestBody['data'] ?? [];

    switch ($eventType) {
        case 'order.created':
            // 处理订单创建事件
            processOrderCreated($data);
            break;
        case 'payment.completed':
            // 处理支付完成事件
            processPaymentCompleted($data);
            break;
        default:
            echo "未知事件类型";
            return;
    }

    http_response_code(200);
    echo "处理成功";
}

// 使用示例
$webhookRequest = [
    'event_type' => 'order.created',
    'timestamp' => time(),
    'order_id' => 'ORD20240217001',
    'user_id' => 123,
    'amount' => 99.00,
    'data' => json_encode(['product_id' => 'PROD001'])
];

// 添加签名
$webhookSecret = 'webhook-secret-key-2024';
$sign = AuthSignFacade::makeSign($webhookRequest, $webhookSecret);
$webhookRequest['sign'] = $sign;

// 处理Webhook
handleWebhook($webhookRequest);
```

### 示例3: 第三方接口调用

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;

/**
 * 调用第三方API
 */
function callThirdPartyAPI(string $url, array $params, string $appKey, string $appSecret): array
{
    // 添加签名
    $params['app_key'] = $appKey;
    $params['timestamp'] = time();
    $params['nonce'] = bin2hex(random_bytes(8));
    $params['sign'] = AuthSignFacade::makeSign($params, $appSecret);

    // 发送请求
    $response = curlPost($url, $params);

    return json_decode($response, true);
}

/**
 * 模拟CURL POST请求
 */
function curlPost(string $url, array $data): string
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response ?: '';
}

// 使用示例
$apiUrl = 'https://api.example.com/v1/orders';
$appKey = 'your-app-key';
$appSecret = 'your-app-secret';

$params = [
    'user_id' => 123,
    'product_id' => 'PROD001',
    'quantity' => 2,
    'amount' => 198.00
];

$response = callThirdPartyAPI($apiUrl, $params, $appKey, $appSecret);
echo "API响应: " . json_encode($response, JSON_UNESCAPED_UNICODE) . PHP_EOL;
```

### 示例4: 敏感操作二次验证

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;

/**
 * 生成敏感操作验证签名
 */
function generateActionSignature(string $userId, string $action, array $actionData): string
{
    $secretKey = 'action-secret-key-' . $userId; // 每个用户独立的密钥

    $params = array_merge([
        'user_id' => $userId,
        'action' => $action,
        'timestamp' => time()
    ], $actionData);

    return AuthSignFacade::makeSign($params, $secretKey);
}

/**
 * 验证敏感操作签名
 */
function verifyActionSignature(string $userId, string $action, array $actionData, string $signature): bool
{
    $secretKey = 'action-secret-key-' . $userId;

    $params = array_merge([
        'user_id' => $userId,
        'action' => $action,
        'timestamp' => $actionData['timestamp'] ?? time()
    ], $actionData);

    $generatedSignature = generateActionSignature($userId, $action, $params);

    return hash_equals($generatedSignature, $signature);
}

// 使用示例
$userId = 'user123';
$action = 'delete_account';
$actionData = [
    'timestamp' => time(),
    'reason' => '自愿注销'
];

// 生成签名
$signature = generateActionSignature($userId, $action, $actionData);
echo "操作签名: " . $signature . PHP_EOL;

// 验证签名
$isValid = verifyActionSignature($userId, $action, $actionData, $signature);
echo "验证结果: " . ($isValid ? '通过' : '失败') . PHP_EOL;
```

### 示例5: 微信公众号接口签名

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;

/**
 * 微信公众号消息加密签名
 */
function generateWeChatSignature(array $params, string $token): string
{
    return AuthSignFacade::makeSign($params, $token);
}

/**
 * 验证微信公众号消息
 */
function verifyWeChatMessage(array $request, string $token): bool
{
    $signature = $request['signature'] ?? '';
    $timestamp = $request['timestamp'] ?? '';
    $nonce = $request['nonce'] ?? '';

    $params = [
        'timestamp' => $timestamp,
        'nonce' => $nonce,
        'token' => $token
    ];

    $generatedSignature = AuthSignFacade::makeSign($params, $token);

    return hash_equals($generatedSignature, $signature);
}

// 使用示例
$weChatToken = 'your-wechat-token';

$request = [
    'signature' => '',
    'timestamp' => time(),
    'nonce' => bin2hex(random_bytes(4)),
    'echostr' => 'test'
];

// 生成签名
$params = [
    'timestamp' => $request['timestamp'],
    'nonce' => $request['nonce'],
    'token' => $weChatToken
];
$request['signature'] = AuthSignFacade::makeSign($params, $weChatToken);

// 验证签名
$isValid = verifyWeChatMessage($request, $weChatToken);
echo "微信消息验证: " . ($isValid ? '成功' : '失败') . PHP_EOL;
```

---

## 注意事项

### 1. 密钥管理

| 场景 | 密钥建议 |
|------|----------|
| API密钥 | 每个应用独立密钥,定期轮换 |
| Webhook | 每个事件类型独立密钥 |
| 用户操作 | 每个用户独立密钥 |
| 第三方对接 | 使用第三方提供的密钥 |

### 2. 时间戳处理

```php
// 添加时间戳参数
$params['timestamp'] = time();

// 验证时间戳有效期(5分钟内)
function verifyTimestamp(int $timestamp, int $expireSeconds = 300): bool
{
    $currentTime = time();
    return ($timestamp > $currentTime - $expireSeconds) &&
           ($timestamp < $currentTime + $expireSeconds);
}
```

### 3. 防重放攻击

```php
// 添加随机数
$params['nonce'] = bin2hex(random_bytes(8));

// 服务器端缓存已使用的nonce(Redis缓存5分钟)
function isNonceUsed(string $nonce): bool
{
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);

    $key = 'api:nonce:' . $nonce;
    if ($redis->exists($key)) {
        return true; // 已使用过
    }

    $redis->setex($key, 300, '1'); // 缓存5分钟
    return false;
}
```

### 4. 安全建议

1. **使用 hash_equals**: 防止时序攻击
2. **HTTPS传输**: 签名和参数必须通过HTTPS传输
3. **密钥保护**: 密钥不要硬编码,使用环境变量或密钥管理系统
4. **参数过滤**: 签名前过滤空值和null值
5. **日志脱敏**: 日志中不要记录完整密钥和签名

### 5. 签名参数排序

签名会自动按参数键名排序,确保签名的一致性:

```php
// 这些参数生成的签名相同
$params1 = ['b' => 2, 'a' => 1, 'c' => 3];
$params2 = ['a' => 1, 'b' => 2, 'c' => 3];

$sign1 = AuthSignFacade::makeSign($params1, 'key');
$sign2 = AuthSignFacade::makeSign($params2, 'key');

echo $sign1 === $sign2 ? '签名相同' : '签名不同'; // 输出: 签名相同
```

---

## 常见问题

### Q1: 签名验证失败怎么办?

A: 检查以下几点:
- 密钥是否正确
- 参数是否一致(包括键名排序)
- 时间戳是否在有效期内
- 是否使用了 `hash_equals` 验证签名

### Q2: 如何处理空数组参数?

A: 空数组参数会生成一个基础签名(仅包含secretKey),这是正常行为。

### Q3: 可以在签名中包含文件参数吗?

A: 不建议。文件参数过大,签名不实际。建议对文件进行MD5或SHA256哈希后对哈希值签名。

### Q4: 签名可以重用吗?

A: 不可以。每个请求都应生成新的签名(通过添加时间戳和随机数实现)。

### Q5: 如何提高安全性?

A: 建议:
- 使用HTTPS传输
- 添加时间戳和随机数防重放
- 密钥定期轮换
- 使用独立的密钥管理系统
- 记录签名验证失败的日志并监控异常

---

## 相关服务

- [KeyManagerFacade](./UseKeyManagerFacade.md) - 密钥管理服务
- [AESFacade](../Secret/UseAESFacade.md) - AES加解密服务
- [RSAFacade](../Secret/UseRSAFacade.md) - RSA加解密服务

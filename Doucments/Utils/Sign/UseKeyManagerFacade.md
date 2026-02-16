# 密钥管理服务使用文档

## 概述

密钥管理服务提供了安全密钥生成功能,支持自定义字符类型和长度,使用密码学安全的随机数生成器。

## 安装

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;
```

## 功能方法

### 1. 生成安全密钥 - `generateSecureSecretKey`

生成密码学安全的随机密钥,可自定义长度和字符类型。

#### 方法签名

```php
KeyManagerFacade::generateSecureSecretKey(int $length = 32, array $charTypes = []): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| length | int | 否 | 密钥长度,默认32位 |
| charTypes | array | 否 | 字符类型数组,默认使用全部类型 |

#### 字符类型

| 类型值 | 说明 | 包含字符 |
|--------|------|----------|
| letters_upper | 大写字母 | A-Z |
| letters_lower | 小写字母 | a-z |
| numbers | 数字 | 0-9 |
| symbols | 特殊符号 | !@#$%^&*()_+-=[]{}|;:,.?~` |

#### 返回值

返回生成的随机密钥字符串。

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

try {
    // 示例1: 生成默认32位密钥(包含所有字符类型)
    $key1 = KeyManagerFacade::generateSecureSecretKey();
    echo "默认密钥: " . $key1 . PHP_EOL;

    // 示例2: 生成16位纯数字密钥
    $key2 = KeyManagerFacade::generateSecureSecretKey(16, ['numbers']);
    echo "数字密钥: " . $key2 . PHP_EOL;

    // 示例3: 生成24位大小写字母密钥
    $key3 = KeyManagerFacade::generateSecureSecretKey(24, ['letters_upper', 'letters_lower']);
    echo "字母密钥: " . $key3 . PHP_EOL;

    // 示例4: 生成40位包含大小写字母和数字的密钥
    $key4 = KeyManagerFacade::generateSecureSecretKey(40, ['letters_upper', 'letters_lower', 'numbers']);
    echo "字母数字密钥: " . $key4 . PHP_EOL;

    // 示例5: 生成包含特殊符号的强密钥
    $key5 = KeyManagerFacade::generateSecureSecretKey(48, ['letters_upper', 'letters_lower', 'numbers', 'symbols']);
    echo "强密钥: " . $key5 . PHP_EOL;

} catch (\InvalidArgumentException $e) {
    echo "生成密钥失败: " . $e->getMessage();
}
```

---

## 完整示例

### 示例1: API接口密钥生成

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

/**
 * 为新用户生成API访问密钥
 */
function generateAPIKeyForUser(int $userId): string
{
    // 生成包含大小写字母、数字和符号的48位密钥
    $apiKey = KeyManagerFacade::generateSecureSecretKey(48, [
        'letters_upper',
        'letters_lower',
        'numbers',
        'symbols'
    ]);

    // 存储到数据库
    saveAPIKeyToDatabase($userId, $apiKey);

    return $apiKey;
}

// 使用示例
$userId = 12345;
$apiKey = generateAPIKeyForUser($userId);
echo "为用户 {$userId} 生成的API密钥: " . $apiKey;
```

### 示例2: JWT令牌密钥生成

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

/**
 * 生成JWT签名密钥
 */
function generateJWTSecret(): string
{
    // JWT推荐使用至少32字节(256位)的密钥
    return KeyManagerFacade::generateSecureSecretKey(64, [
        'letters_upper',
        'letters_lower',
        'numbers',
        'symbols'
    ]);
}

// 使用示例
$jwtSecret = generateJWTSecret();
echo "JWT密钥: " . $jwtSecret . PHP_EOL;
echo "请将此密钥配置到环境变量中!";
```

### 示例3: 临时验证码生成

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

/**
 * 生成临时验证码
 */
function generateTempCode(int $length = 8): string
{
    // 生成纯数字验证码
    return KeyManagerFacade::generateSecureSecretKey($length, ['numbers']);
}

/**
 * 生成临时令牌
 */
function generateTempToken(): string
{
    // 生成32位字母数字令牌
    return KeyManagerFacade::generateSecureSecretKey(32, [
        'letters_upper',
        'letters_lower',
        'numbers'
    ]);
}

// 使用示例
$code = generateTempCode(6);
echo "验证码: " . $code . PHP_EOL;

$token = generateTempToken();
echo "临时令牌: " . $token . PHP_EOL;
```

### 示例4: 数据库加密密钥生成

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

/**
 * 为敏感数据字段生成加密密钥
 */
function generateDataEncryptionKey(string $fieldType): string
{
    $charTypes = ['letters_upper', 'letters_lower', 'numbers', 'symbols'];

    switch ($fieldType) {
        case 'low':
            // 低敏感度: 32位
            return KeyManagerFacade::generateSecureSecretKey(32, $charTypes);
        case 'medium':
            // 中敏感度: 48位
            return KeyManagerFacade::generateSecureSecretKey(48, $charTypes);
        case 'high':
            // 高敏感度: 64位
            return KeyManagerFacade::generateSecureSecretKey(64, $charTypes);
        default:
            return KeyManagerFacade::generateSecureSecretKey(48, $charTypes);
    }
}

// 使用示例
$phoneKey = generateDataEncryptionKey('low');
echo "手机号加密密钥: " . $phoneKey . PHP_EOL;

$idCardKey = generateDataEncryptionKey('high');
echo "身份证加密密钥: " . $idCardKey . PHP_EOL;
```

### 示例5: OAuth客户端密钥生成

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

/**
 * 生成OAuth客户端ID
 */
function generateOAuthClientId(): string
{
    // 生成32位字母数字ID
    return KeyManagerFacade::generateSecureSecretKey(32, [
        'letters_lower',
        'numbers'
    ]);
}

/**
 * 生成OAuth客户端密钥
 */
function generateOAuthClientSecret(): string
{
    // 生成64位强密钥
    return KeyManagerFacade::generateSecureSecretKey(64, [
        'letters_upper',
        'letters_lower',
        'numbers',
        'symbols'
    ]);
}

// 使用示例
$clientId = generateOAuthClientId();
$clientSecret = generateOAuthClientSecret();

echo "OAuth Client ID: " . $clientId . PHP_EOL;
echo "OAuth Client Secret: " . $clientSecret . PHP_EOL;
```

---

## 错误处理

### 常见异常

| 异常类型 | 说明 |
|----------|------|
| InvalidArgumentException | 字符类型配置错误,无法生成有效字符池 |

### 错误处理示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

try {
    // 错误示例: 传入无效的字符类型
    $key = KeyManagerFacade::generateSecureSecretKey(32, ['invalid_type']);

} catch (\InvalidArgumentException $e) {
    echo "参数错误: " . $e->getMessage();
    // 输出: 参数错误: 字符类型配置错误,无法生成有效字符池
}

try {
    // 错误示例: 传入空数组
    $key = KeyManagerFacade::generateSecureSecretKey(32, []);

} catch (\InvalidArgumentException $e) {
    echo "参数错误: " . $e->getMessage();
}
```

---

## 注意事项

### 1. 密钥长度建议

| 用途 | 推荐长度 | 说明 |
|------|----------|------|
| API密钥 | 32-64位 | 平衡安全性和易用性 |
| JWT密钥 | 64位+ | 符合JWT标准要求 |
| 临时验证码 | 6-8位 | 便于用户输入 |
| 临时令牌 | 32-48位 | 足够的随机性 |
| 数据库加密 | 48-64位 | 高安全性要求 |

### 2. 密钥存储建议

1. **敏感密钥**: 存储在环境变量或密钥管理系统中
2. **数据库密钥**: 加密后存储,不要明文保存
3. **API密钥**: 在数据库中哈希存储或使用加密服务
4. **临时密钥**: 设置过期时间,及时清理

### 3. 安全建议

1. **使用全部字符类型**: 包含大小写字母、数字和符号的密钥更安全
2. **足够的长度**: 至少32位,高安全需求建议64位+
3. **避免重复**: 确保每次生成的密钥都是唯一的
4. **密钥轮换**: 定期更换密钥提高安全性
5. **密钥分离**: 不同用途使用不同密钥

### 4. 密钥强度评估

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;

// 低强度: 仅数字
$key1 = KeyManagerFacade::generateSecureSecretKey(16, ['numbers']);

// 中强度: 字母+数字
$key2 = KeyManagerFacade::generateSecureSecretKey(32, [
    'letters_upper',
    'letters_lower',
    'numbers'
]);

// 高强度: 全部字符类型
$key3 = KeyManagerFacade::generateSecureSecretKey(48, [
    'letters_upper',
    'letters_lower',
    'numbers',
    'symbols'
]);
```

---

## 性能考虑

1. **生成速度**: 使用 `random_bytes()` 函数,性能良好
2. **批量生成**: 如需批量生成,建议循环调用
3. **缓存考虑**: 密钥不应缓存,每次都应重新生成
4. **并发安全**: 生成的密钥具有足够的随机性,可并发使用

---

## 常见问题

### Q1: 生成的密钥是否保证唯一?

A: 使用 `random_bytes()` 生成,在正常使用场景下几乎可以保证唯一。如需严格保证唯一,可在生成后检查数据库中是否存在重复。

### Q2: 密钥长度越长越安全吗?

A: 是的,但也要考虑使用场景。建议参考"密钥长度建议"表格选择合适的长度。

### Q3: 可以生成中文密钥吗?

A: 不可以。密钥只包含指定的ASCII字符,这是为了保证密钥在各种系统中的兼容性。

### Q4: 生成的密钥安全吗?

A: 使用 PHP 的 `random_bytes()` 函数,这是密码学安全的随机数生成器,适用于生成密钥等敏感数据。

---

## 相关服务

- [AuthSignFacade](./UseAuthSignFacade.md) - 认证签名服务
- [AESFacade](../Secret/UseAESFacade.md) - AES加解密服务
- [RSAFacade](../Secret/UseRSAFacade.md) - RSA加解密服务

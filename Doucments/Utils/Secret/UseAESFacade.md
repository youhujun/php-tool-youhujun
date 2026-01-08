# AES加解密服务使用文档

## 概述

AES加解密服务提供了 AES 算法的加密和解密功能,支持多种加密模式和自定义配置。

## 安装

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;
```

## 功能方法

### 1. 加密 - `encrypt`

使用AES算法加密字符串。

#### 方法签名

```php
AESFacade::encrypt(string $data, string $key, ?string $method = null, ?string $iv = null): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| data | string | 是 | 待加密的字符串 |
| key | string | 是 | 加密密钥 |
| method | string|null | 否 | 加密方法,默认 AES-256-CBC |
| iv | string|null | 否 | 初始化向量,默认 '0123456789ABEDEF' |

#### 返回值

返回 Base64 编码后的加密结果字符串。

#### 支持的加密方法

- `AES-128-CBC` - 128位密钥,CBC模式
- `AES-192-CBC` - 192位密钥,CBC模式
- `AES-256-CBC` - 256位密钥,CBC模式(默认)
- `AES-128-ECB` - 128位密钥,ECB模式
- `AES-192-ECB` - 192位密钥,ECB模式
- `AES-256-ECB` - 256位密钥,ECB模式

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;

try {
    // 基础加密 - 使用默认参数
    $data = 'Hello World';
    $key = 'my-secret-key-1234567890123456'; // 密钥长度需根据加密方法匹配

    $encrypted = AESFacade::encrypt($data, $key);
    echo "加密结果: " . $encrypted;

    // 自定义加密方法
    $encrypted = AESFacade::encrypt($data, $key, 'AES-128-CBC');
    echo "AES-128-CBC 加密: " . $encrypted;

    // 自定义IV
    $customIV = '0000111122223333';
    $encrypted = AESFacade::encrypt($data, $key, 'AES-256-CBC', $customIV);
    echo "自定义IV加密: " . $encrypted;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "加密失败: " . $e->getMessage();
}
```

---

### 2. 解密 - `decrypt`

使用AES算法解密字符串。

#### 方法签名

```php
AESFacade::decrypt(string $data, string $key, ?string $method = null, ?string $iv = null): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| data | string | 是 | 需要解密的Base64编码字符串 |
| key | string | 是 | 解密密钥 |
| method | string|null | 否 | 加密方法,默认 AES-256-CBC |
| iv | string|null | 否 | 初始化向量,默认 '0123456789ABEDEF' |

#### 返回值

返回解密后的原始字符串。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;

try {
    // 基础解密 - 使用默认参数
    $encrypted = '加密后的Base64字符串';
    $key = 'my-secret-key-1234567890123456';

    $decrypted = AESFacade::decrypt($encrypted, $key);
    echo "解密结果: " . $decrypted;

    // 自定义加密方法解密
    $decrypted = AESFacade::decrypt($encrypted, $key, 'AES-128-CBC');
    echo "AES-128-CBC 解密: " . $decrypted;

    // 自定义IV解密
    $customIV = '0000111122223333';
    $decrypted = AESFacade::decrypt($encrypted, $key, 'AES-256-CBC', $customIV);
    echo "自定义IV解密: " . $decrypted;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "解密失败: " . $e->getMessage();
}
```

---

## 完整示例

### 示例1: 基础加解密

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;

// 待加密的数据
$originalData = '这是一段需要加密的敏感数据';
$key = 'my-secret-key-1234567890123456'; // 32字节密钥,对应AES-256

try {
    // 加密
    $encrypted = AESFacade::encrypt($originalData, $key);
    echo "加密结果: " . $encrypted . PHP_EOL;

    // 解密
    $decrypted = AESFacade::decrypt($encrypted, $key);
    echo "解密结果: " . $decrypted . PHP_EOL;

    // 验证
    if ($decrypted === $originalData) {
        echo "加解密验证成功!";
    }

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "操作失败: " . $e->getMessage();
}
```

### 示例2: 使用服务类(非静态)

```php
<?php

use YouHuJun\Tool\App\Service\V1\Utils\Secret\AESFacadeService;

// 创建服务实例
$aesService = new AESFacadeService();

// 配置加密参数
$aesService->setMethod('AES-128-CBC');
$aesService->setIV('my-custom-iv-123');

$data = 'Hello World';
$key = 'my-secret-key-1234'; // 16字节密钥,对应AES-128

try {
    // 加密
    $encrypted = $aesService->encrypt($data, $key);
    echo "加密结果: " . $encrypted . PHP_EOL;

    // 解密
    $decrypted = $aesService->decrypt($encrypted, $key);
    echo "解密结果: " . $decrypted . PHP_EOL;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "操作失败: " . $e->getMessage();
}
```

### 示例3: 构造函数初始化

```php
<?php

use YouHuJun\Tool\App\Service\V1\Utils\Secret\AESFacadeService;

// 通过构造函数初始化
$aesService = new AESFacadeService('AES-128-CBC', 'my-custom-iv-123');

$data = '测试数据';
$key = 'my-secret-key-1234';

try {
    $encrypted = $aesService->encrypt($data, $key);
    $decrypted = $aesService->decrypt($encrypted, $key);

    echo "成功! " . $decrypted;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "失败: " . $e->getMessage();
}
```

### 示例4: 用户密码加密

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;

/**
 * 加密用户敏感信息
 */
function encryptUserData(string $data, string $userKey): ?string
{
    try {
        $appKey = 'app-global-secret-key-123456'; // 应用全局密钥
        $finalKey = md5($appKey . $userKey); // 组合密钥

        return AESFacade::encrypt($data, $finalKey, 'AES-256-CBC');
    } catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
        error_log("用户数据加密失败: " . $e->getMessage());
        return null;
    }
}

/**
 * 解密用户敏感信息
 */
function decryptUserData(string $encrypted, string $userKey): ?string
{
    try {
        $appKey = 'app-global-secret-key-123456'; // 应用全局密钥
        $finalKey = md5($appKey . $userKey); // 组合密钥

        return AESFacade::decrypt($encrypted, $finalKey, 'AES-256-CBC');
    } catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
        error_log("用户数据解密失败: " . $e->getMessage());
        return null;
    }
}

// 使用示例
$userKey = 'user-unique-key-123';
$userData = '用户的身份证号:123456789012345678';

$encrypted = encryptUserData($userData, $userKey);
$decrypted = decryptUserData($encrypted, $userKey);

echo "解密结果: " . $decrypted;
```

---

## 错误处理

所有方法在失败时都会抛出 `CommonException` 异常,建议使用 try-catch 捕获。

### 常见错误码

| 错误码 | 错误信息 | 说明 |
|--------|----------|------|
| 90000 | AESMethodError | AES加密方法无效 |
| 90010 | AESEncryptDataEmpty | AES加密数据为空 |
| 90020 | AESEncryptKeyEmpty | AES加密密钥为空 |
| 90030 | AESEncryptFailed | AES加密失败 |
| 90040 | AESDecryptDataEmpty | AES解密数据为空 |
| 90050 | AESDecryptKeyEmpty | AES解密密钥为空 |
| 90060 | AESDecryptDataInvalid | AES解密数据格式无效 |
| 90070 | AESDecryptFailed | AES解密失败 |

### 错误处理示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;

try {
    $encrypted = AESFacade::encrypt($data, $key);
} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    $errorCode = $e->getCode();
    $errorMsg = $e->getMessage();

    switch ($errorCode) {
        case 90010:
            // 数据为空
            break;
        case 90020:
            // 密钥为空
            break;
        case 90030:
            // 加密失败,可能是密钥或IV长度不匹配
            break;
        default:
            // 其他错误
            break;
    }
}
```

---

## 注意事项

### 1. 密钥长度

不同的加密方法需要不同长度的密钥:

| 加密方法 | 密钥长度 | 说明 |
|----------|----------|------|
| AES-128 | 16字节 | 需要使用16位密钥 |
| AES-192 | 24字节 | 需要使用24位密钥 |
| AES-256 | 32字节 | 需要使用32位密钥 |

### 2. IV长度

- CBC模式: IV长度必须与密钥长度相同(16/24/32字节)
- ECB模式: 不需要IV

### 3. 安全建议

1. **密钥管理**: 密钥应存储在安全的地方,不要硬编码在代码中
2. **密钥轮换**: 定期更换密钥提高安全性
3. **IV唯一性**: 每次加密使用不同的IV更安全,CBC模式下建议使用随机IV
4. **避免ECB模式**: ECB模式安全性较低,推荐使用CBC模式
5. **数据验证**: 解密后应验证数据格式和有效性

### 4. 性能考虑

- 加密/解密操作有一定的性能开销
- 对于大量数据,考虑使用更高效的算法或硬件加速
- 缓存加密结果时注意安全性

---

## API对比

| 功能 | 原方法 | 新方法 | 说明 |
|------|--------|--------|------|
| 加密 | `encodeAES()` | `encrypt()` | 方法名更符合命名规范 |
| 解密 | `decodeAES()` | `decrypt()` | 方法名更符合命名规范 |
| 设置方法 | `setMethod()` | `setMethod()` | 保持一致 |
| 设置IV | `setIV()` | `setIV()` | 保持一致 |
| 参数传递 | 类成员变量 | 方法参数 | 更灵活,无状态 |

---

## 迁移指南

如果您之前使用的是 `encodeAES()` 和 `decodeAES()` 方法,可以按以下方式迁移:

### 旧代码

```php
$aesService = new AESFacadeService();
$aesService->setMethod('AES-256-CBC');
$aesService->setIV('0123456789ABEDEF');

$encrypted = $aesService->encodeAES($data, $key);
$decrypted = $aesService->decodeAES($encrypted, $key);
```

### 新代码

```php
// 方式1: 使用静态门面(推荐)
$encrypted = AESFacade::encrypt($data, $key);
$decrypted = AESFacade::decrypt($encrypted, $key);

// 方式2: 使用服务类
$aesService = new AESFacadeService('AES-256-CBC', '0123456789ABEDEF');
$encrypted = $aesService->encrypt($data, $key);
$decrypted = $aesService->decrypt($encrypted, $key);
```

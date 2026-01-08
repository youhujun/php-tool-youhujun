# RSA加解密服务使用文档

## 概述

RSA加解密服务提供了 RSA 非对称加密算法的公钥加密和私钥解密功能,支持多种密钥格式。

## 安装

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;
```

## 功能方法

### 1. 公钥加密 - `encrypt`

使用RSA公钥加密数据。

#### 方法签名

```php
RSAFacade::encrypt(
    string $data,
    string $publicKeyString,
    string $format = 'base64',
    int $padding = OPENSSL_PKCS1_PADDING
): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| data | string | 是 | 待加密的明文字符串 |
| publicKeyString | string | 是 | 公钥字符串 |
| format | string | 否 | 公钥格式: pem(默认), der, base64 |
| padding | int | 否 | 填充方式,默认 OPENSSL_PKCS1_PADDING |

#### 返回值

返回 Base64 编码的加密结果字符串。

#### 支持的密钥格式

- `pem` - PEM格式,包含 -----BEGIN PUBLIC KEY----- 头尾
- `der` - DER格式二进制数据
- `base64` - Base64编码的DER格式数据(默认)

#### 支持的填充方式

- `OPENSSL_PKCS1_PADDING` - PKCS#1 填充(默认)
- `OPENSSL_NO_PADDING` - 无填充
- `OPENSSL_SSLV23_PADDING` - SSLv23 填充

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

// 假设这是从文件或配置中读取的公钥
$publicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...';

try {
    $data = '这是一段需要加密的敏感数据';

    // 使用默认格式(base64)和默认填充方式加密
    $encrypted = RSAFacade::encrypt($data, $publicKey);
    echo "加密结果: " . $encrypted;

    // 使用PEM格式加密
    $pemPublicKey = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----";
    $encrypted = RSAFacade::encrypt($data, $pemPublicKey, 'pem');
    echo "PEM格式加密: " . $encrypted;

    // 使用无填充方式加密
    $encrypted = RSAFacade::encrypt($data, $publicKey, 'base64', OPENSSL_NO_PADDING);
    echo "无填充加密: " . $encrypted;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "加密失败: " . $e->getMessage();
}
```

---

### 2. 私钥解密 - `decrypt`

使用RSA私钥解密数据。

#### 方法签名

```php
RSAFacade::decrypt(
    string $data,
    string $privateKeyString,
    string $format = 'base64',
    ?string $passphrase = null,
    int $padding = OPENSSL_PKCS1_PADDING
): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| data | string | 是 | Base64编码的加密字符串 |
| privateKeyString | string | 是 | 私钥字符串 |
| format | string | 否 | 私钥格式: pem(默认), der, base64 |
| passphrase | string|null | 否 | 私钥密码,如果有 |
| padding | int | 否 | 填充方式,默认 OPENSSL_PKCS1_PADDING |

#### 返回值

返回解密后的原始字符串。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

// 假设这是从文件或配置中读取的私钥
$privateKey = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC...';

try {
    $encrypted = '加密后的Base64字符串';

    // 使用默认格式(base64)和默认填充方式解密
    $decrypted = RSAFacade::decrypt($encrypted, $privateKey);
    echo "解密结果: " . $decrypted;

    // 使用PEM格式解密
    $pemPrivateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC...\n-----END PRIVATE KEY-----";
    $decrypted = RSAFacade::decrypt($encrypted, $pemPrivateKey, 'pem');
    echo "PEM格式解密: " . $decrypted;

    // 解密带密码的私钥
    $encrypted = RSAFacade::encrypt($data, $publicKey);
    $decrypted = RSAFacade::decrypt($encrypted, $privateKey, 'base64', 'your_passphrase');
    echo "带密码解密: " . $decrypted;

    // 使用无填充方式解密
    $decrypted = RSAFacade::decrypt($encrypted, $privateKey, 'base64', null, OPENSSL_NO_PADDING);
    echo "无填充解密: " . $decrypted;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "解密失败: " . $e->getMessage();
}
```

---

## 完整示例

### 示例1: 基础加解密

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

// 假设这是你的公钥和私钥
$publicKey = 'YOUR_PUBLIC_KEY_BASE64';
$privateKey = 'YOUR_PRIVATE_KEY_BASE64';

$originalData = '这是一段需要加密的敏感数据';

try {
    // 加密
    $encrypted = RSAFacade::encrypt($originalData, $publicKey);
    echo "加密结果: " . $encrypted . PHP_EOL;

    // 解密
    $decrypted = RSAFacade::decrypt($encrypted, $privateKey);
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

use YouHuJun\Tool\App\Service\V1\Utils\Secret\RSAFacadeService;

// 创建服务实例
$rsaService = new RSAFacadeService();

$publicKey = 'YOUR_PUBLIC_KEY';
$privateKey = 'YOUR_PRIVATE_KEY';
$data = 'Hello World';

try {
    // 加密
    $encrypted = $rsaService->encrypt($data, $publicKey);
    echo "加密结果: " . $encrypted . PHP_EOL;

    // 解密
    $decrypted = $rsaService->decrypt($encrypted, $privateKey);
    echo "解密结果: " . $decrypted . PHP_EOL;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "操作失败: " . $e->getMessage();
}
```

### 示例3: 从文件读取密钥

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

/**
 * 从文件读取PEM格式密钥
 */
function readKeyFromFile(string $filePath): string
{
    if (!file_exists($filePath)) {
        throw new Exception('密钥文件不存在');
    }

    return file_get_contents($filePath);
}

try {
    // 读取PEM格式密钥
    $publicKey = readKeyFromFile('/path/to/public.pem');
    $privateKey = readKeyFromFile('/path/to/private.pem');

    $data = '测试数据';

    // 加密
    $encrypted = RSAFacade::encrypt($data, $publicKey, 'pem');

    // 解密
    $decrypted = RSAFacade::decrypt($encrypted, $privateKey, 'pem');

    echo "成功! " . $decrypted;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "失败: " . $e->getMessage();
}
```

### 示例4: API数据加密传输

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

/**
 * 加密API请求数据
 */
function encryptApiRequest(array $requestData, string $publicKey): ?string
{
    try {
        $json = json_encode($requestData);
        return RSAFacade::encrypt($json, $publicKey);
    } catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
        error_log("API请求数据加密失败: " . $e->getMessage());
        return null;
    }
}

/**
 * 解密API响应数据
 */
function decryptApiResponse(string $encrypted, string $privateKey): ?array
{
    try {
        $json = RSAFacade::decrypt($encrypted, $privateKey);
        return json_decode($json, true);
    } catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
        error_log("API响应数据解密失败: " . $e->getMessage());
        return null;
    }
}

// 使用示例
$publicKey = 'PARTNER_PUBLIC_KEY';
$privateKey = 'YOUR_PRIVATE_KEY';

// 准备请求数据
$requestData = [
    'user_id' => 12345,
    'username' => 'testuser',
    'timestamp' => time()
];

// 加密请求数据
$encryptedRequest = encryptApiRequest($requestData, $publicKey);

// 发送加密数据到API...
// 假设API返回加密响应 $encryptedResponse

// 解密响应数据
$responseData = decryptApiResponse($encryptedResponse, $privateKey);
print_r($responseData);
```

---

## 错误处理

所有方法在失败时都会抛出 `CommonException` 异常,建议使用 try-catch 捕获。

### 常见错误码

| 错误码 | 错误信息 | 说明 |
|--------|----------|------|
| 91000 | RSAPublicKeyEmpty | RSA公钥为空 |
| 91010 | RSAPublicKeyFormatNotSupported | RSA公钥格式不支持 |
| 91020 | RSAPublicKeyBase64DecodeFailed | RSA公钥Base64解码失败 |
| 91030 | RSAPublicKeyLoadFailed | RSA公钥加载失败 |
| 91040 | RSAPrivateKeyEmpty | RSA私钥为空 |
| 91050 | RSAPrivateKeyFormatNotSupported | RSA私钥格式不支持 |
| 91060 | RSAPrivateKeyBase64DecodeFailed | RSA私钥Base64解码失败 |
| 91070 | RSAPrivateKeyLoadFailed | RSA私钥加载失败 |
| 91080 | RSAEncryptDataEmpty | RSA加密数据为空 |
| 91090 | RSAEncryptFailed | RSA加密失败 |
| 91100 | RSAEncryptException | RSA加密异常 |
| 91110 | RSADecryptDataEmpty | RSA解密数据为空 |
| 91120 | RSADecryptDataInvalid | RSA解密数据格式无效 |
| 91130 | RSADecryptFailed | RSA解密失败 |
| 91140 | RSADecryptException | RSA解密异常 |

### 错误处理示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

try {
    $encrypted = RSAFacade::encrypt($data, $publicKey);
} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    $errorCode = $e->getCode();
    $errorMsg = $e->getMessage();

    switch ($errorCode) {
        case 91000:
            // 公钥为空
            break;
        case 91030:
            // 公钥加载失败,检查公钥格式
            break;
        case 91090:
            // 加密失败,可能是数据过长或密钥不匹配
            break;
        default:
            // 其他错误
            break;
    }
}
```

---

## 注意事项

### 1. 数据长度限制

RSA加密对数据长度有限制:

- RSA-1024: 最多加密 117 字节(使用PKCS1填充)
- RSA-2048: 最多加密 245 字节(使用PKCS1填充)
- RSA-4096: 最多加密 501 字节(使用PKCS1填充)

**解决方案**: 对于长数据,应使用RSA加密对称密钥(如AES密钥),然后用AES加密实际数据(混合加密)。

### 2. 密钥管理

1. **安全存储**: 私钥应存储在安全的位置,不要硬编码在代码中
2. **权限控制**: 私钥文件应设置适当的文件权限(如 600)
3. **密钥轮换**: 定期更换密钥提高安全性
4. **备份**: 做好密钥备份,避免丢失

### 3. 性能考虑

- RSA加密/解密比AES慢得多
- 对于频繁操作,考虑使用混合加密方案
- 缓存加密结果时注意安全性

### 4. 安全建议

1. **使用足够长的密钥**: 推荐至少2048位
2. **填充方式**: 推荐使用PKCS1填充
3. **避免直接加密敏感数据**: 建议混合加密
4. **验证数据完整性**: 结合签名验证数据完整性

---

## 密钥格式对比

### PEM格式

```pem
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvFyZk...
-----END PUBLIC KEY-----
```

**特点**: 可读性好,包含头尾标识,常用格式

### DER格式

二进制格式,可读性差,但体积小

### Base64格式

DER格式的Base64编码,便于传输和存储

**示例**:
```php
// DER转Base64
$base64Key = base64_encode($derKey);

// Base64转DER
$derKey = base64_decode($base64Key);
```

---

## 混合加密示例

RSA + AES 混合加密,适合加密大数据:

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

/**
 * 混合加密: RSA加密AES密钥, AES加密数据
 */
function hybridEncrypt(string $data, string $publicKey): array
{
    // 生成随机AES密钥
    $aesKey = bin2hex(random_bytes(16)); // 32字节,对应AES-256

    // 使用AES加密数据
    $encryptedData = AESFacade::encrypt($data, $aesKey, 'AES-256-CBC');

    // 使用RSA加密AES密钥
    $encryptedKey = RSAFacade::encrypt($aesKey, $publicKey);

    return [
        'encrypted_key' => $encryptedKey,
        'encrypted_data' => $encryptedData
    ];
}

/**
 * 混合解密: RSA解密AES密钥, AES解密数据
 */
function hybridDecrypt(array $encrypted, string $privateKey): string
{
    // 使用RSA解密AES密钥
    $aesKey = RSAFacade::decrypt($encrypted['encrypted_key'], $privateKey);

    // 使用AES解密数据
    $data = AESFacade::decrypt($encrypted['encrypted_data'], $aesKey, 'AES-256-CBC');

    return $data;
}

// 使用示例
$publicKey = 'YOUR_PUBLIC_KEY';
$privateKey = 'YOUR_PRIVATE_KEY';
$longData = file_get_contents('/path/to/large/file.txt');

// 加密
$encrypted = hybridEncrypt($longData, $publicKey);

// 解密
$decrypted = hybridDecrypt($encrypted, $privateKey);

echo "解密成功: " . substr($decrypted, 0, 100) . '...';
```

---

## 生成RSA密钥对

可以使用 OpenSSL 命令生成密钥对:

### 生成私钥

```bash
# 生成2048位RSA私钥(PEM格式)
openssl genrsa -out private.pem 2048

# 生成带密码的私钥
openssl genrsa -aes256 -out private.pem 2048
```

### 生成公钥

```bash
# 从私钥提取公钥
openssl rsa -in private.pem -pubout -out public.pem
```

### 转换格式

```bash
# PEM转DER
openssl rsa -in public.pem -pubout -outform DER -out public.der

# PEM转Base64
base64 public.pem > public.base64

# DER转Base64
base64 public.der > public.base64
```

---

## API对比

| 功能 | 原方法 | 新方法 | 说明 |
|------|--------|--------|------|
| 获取公钥 | `rsaGetPublicKey()` | `getPublicKey()` | 方法名调整,支持多种格式 |
| 公钥加密 | `rsaEncrypt()` | `encrypt()` | 方法名调整,支持多种填充方式 |
| 私钥解密 | 无 | `decrypt()` | 新增私钥解密方法 |
| 获取私钥 | 无 | `getPrivateKey()` | 新增获取私钥方法 |
| 参数传递 | 类成员变量 | 方法参数 | 更灵活,无状态 |

---

## 迁移指南

如果您之前使用的是 `rsaEncrypt()` 方法,可以按以下方式迁移:

### 旧代码

```php
$aesService = new RSAFacadeService();
$encrypted = $aesService->rsaEncrypt($data, $publicKeyStr);
```

### 新代码

```php
// 方式1: 使用静态门面(推荐)
$encrypted = RSAFacade::encrypt($data, $publicKeyStr);

// 方式2: 使用服务类
$rsaService = new RSAFacadeService();
$encrypted = $rsaService->encrypt($data, $publicKeyStr);
```

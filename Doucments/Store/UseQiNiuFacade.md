# 七牛云存储服务使用文档

## 概述

七牛云存储服务提供了文件上传、删除、获取文件信息和下载链接生成等功能。

## 依赖说明

本服务依赖七牛云官方SDK:

```bash
composer require qiniu/php-sdk
```

## 安装

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;
```

## 快速开始

### 1. 初始化配置

在使用静态门面之前,需要先初始化配置:

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 初始化七牛云配置
QiNiuFacade::init(
    'YOUR_QINIU_ACCESS_KEY',
    'YOUR_QINIU_SECRET_KEY',
    'https://your-cdn-domain.com/',
    7200  // 上传凭证有效期(秒)
);
```

### 2. 上传文件

```php
// 上传本地文件
$result = QiNiuFacade::uploadFile(
    '/path/to/local/file.jpg',
    'your-bucket-name',
    'uploads/2025/01/file.jpg'
);

print_r($result);
```

---

## 功能方法

### 1. 初始化配置 - `init`

在使用静态门面之前,必须先初始化配置。

#### 方法签名

```php
QiNiuFacade::init(string $accessKey, string $secretKey, ?string $cdnUrl = null, int $expires = 7200): void
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| accessKey | string | 是 | 七牛云AccessKey |
| secretKey | string | 是 | 七牛云SecretKey |
| cdnUrl | string|null | 否 | CDN域名 |
| expires | int | 否 | 上传凭证有效期(秒),默认7200(2小时) |

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

QiNiuFacade::init(
    'YOUR_QINIU_ACCESS_KEY',
    'YOUR_QINIU_SECRET_KEY',
    'https://cdn.example.com/',
    3600  // 1小时
);
```

---

### 2. 上传文件 - `uploadFile`

上传本地文件到七牛云存储空间。

#### 方法签名

```php
QiNiuFacade::uploadFile(
    string $filePath,
    string $bucket,
    string $savePath,
    ?string $mimeType = null,
    bool $checkCrc = true
): array
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| filePath | string | 是 | 本地文件完整路径 |
| bucket | string | 是 | 存储空间名称 |
| savePath | string | 是 | 保存路径(文件key) |
| mimeType | string|null | 否 | MIME类型,默认 application/octet-stream |
| checkCrc | bool | 否 | 是否校验CRC32,默认true |

#### 返回值

返回上传结果的数组,包含 key, hash, fsize, name 等信息。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 基础上传
$result = QiNiuFacade::uploadFile(
    '/var/www/uploads/image.jpg',
    'my-bucket',
    'images/2025/01/image.jpg'
);

echo "文件key: " . $result['key'];
echo "文件hash: " . $result['hash'];
echo "文件大小: " . $result['fsize'];

// 指定MIME类型
$result = QiNiuFacade::uploadFile(
    '/var/www/uploads/image.jpg',
    'my-bucket',
    'images/photo.jpg',
    'image/jpeg'
);

// 不校验CRC32(提高上传速度)
$result = QiNiuFacade::uploadFile(
    '/var/www/uploads/image.jpg',
    'my-bucket',
    'images/photo.jpg',
    'image/jpeg',
    false
);
```

---

### 3. 上传数据 - `uploadData`

直接上传二进制数据到七牛云存储空间。

#### 方法签名

```php
QiNiuFacade::uploadData(
    string $data,
    string $bucket,
    string $savePath,
    ?string $mimeType = null
): array
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| data | string | 是 | 文件内容(二进制) |
| bucket | string | 是 | 存储空间名称 |
| savePath | string | 是 | 保存路径(文件key) |
| mimeType | string|null | 否 | MIME类型,默认 application/octet-stream |

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 上传字符串
$content = 'Hello, Qiniu Cloud!';
$result = QiNiuFacade::uploadData(
    $content,
    'my-bucket',
    'text/hello.txt',
    'text/plain'
);

// 上传图片二进制数据
$imageData = file_get_contents('/path/to/image.jpg');
$result = QiNiuFacade::uploadData(
    $imageData,
    'my-bucket',
    'images/upload.jpg',
    'image/jpeg'
);

// 上传Base64解码后的数据
$base64Data = 'iVBORw0KGgoAAAANSUhEUgAA...';
$imageData = base64_decode($base64Data);
$result = QiNiuFacade::uploadData(
    $imageData,
    'my-bucket',
    'images/decoded.jpg',
    'image/jpeg'
);
```

---

### 4. 获取私有空间下载链接 - `getPrivateFileUrl`

获取私有空间的签名下载链接。

#### 方法签名

```php
QiNiuFacade::getPrivateFileUrl(string $savePath, ?int $expires = null): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| savePath | string | 是 | 文件保存路径(key) |
| expires | int|null | 否 | 有效期(秒),默认3600(1小时) |

#### 返回值

返回签名后的下载URL。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 获取默认有效期(1小时)的下载链接
$url = QiNiuFacade::getPrivateFileUrl('images/2025/01/photo.jpg');
echo $url;

// 获取自定义有效期的下载链接
$url = QiNiuFacade::getPrivateFileUrl('images/2025/01/photo.jpg', 86400); // 24小时
echo $url;
```

---

### 5. 获取公有空间下载链接 - `getPublicFileUrl`

获取公有空间的下载链接。

#### 方法签名

```php
QiNiuFacade::getPublicFileUrl(string $savePath): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| savePath | string | 是 | 文件保存路径(key) |

#### 返回值

返回下载URL。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

$url = QiNiuFacade::getPublicFileUrl('images/2025/01/photo.jpg');
echo $url;
// 输出: https://cdn.example.com/images/2025/01/photo.jpg
```

---

### 6. 删除文件 - `deleteFile`

删除七牛云存储空间中的文件。

#### 方法签名

```php
QiNiuFacade::deleteFile(string $bucket, string $savePath): bool
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| bucket | string | 是 | 存储空间名称 |
| savePath | string | 是 | 文件key |

#### 返回值

删除成功返回true。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

$success = QiNiuFacade::deleteFile('my-bucket', 'images/2025/01/photo.jpg');

if ($success) {
    echo "文件删除成功";
}
```

---

### 7. 获取文件信息 - `getFileInfo`

获取七牛云存储空间中文件的详细信息。

#### 方法签名

```php
QiNiuFacade::getFileInfo(string $bucket, string $savePath): array
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| bucket | string | 是 | 存储空间名称 |
| savePath | string | 是 | 文件key |

#### 返回值

返回文件信息数组,包含:
- `hash`: 文件ETag值
- `fsize`: 文件大小(字节)
- `mimeType`: MIME类型
- `putTime`: 上传时间(微秒时间戳)
- `type`: 文件类型(0:普通文件, 1:归档文件)

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

$fileInfo = QiNiuFacade::getFileInfo('my-bucket', 'images/2025/01/photo.jpg');

echo "文件hash: " . $fileInfo['hash'];
echo "文件大小: " . $fileInfo['fsize'] . " 字节";
echo "MIME类型: " . $fileInfo['mimeType'];
echo "上传时间: " . date('Y-m-d H:i:s', $fileInfo['putTime'] / 10000000);
```

---

### 8. 获取上传凭证 - `getUploadToken`

获取上传凭证,供客户端直传使用。

#### 方法签名

```php
QiNiuFacade::getUploadToken(string $bucket, ?string $keyToOverwrite = null): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| bucket | string | 是 | 存储空间名称 |
| keyToOverwrite | string|null | 否 | 覆盖上传的文件名,不传则不覆盖 |

#### 返回值

返回上传凭证字符串。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 获取上传凭证(不覆盖)
$token = QiNiuFacade::getUploadToken('my-bucket');
echo $token;

// 获取上传凭证(覆盖指定文件)
$token = QiNiuFacade::getUploadToken('my-bucket', 'images/2025/01/photo.jpg');
echo $token;
```

---

### 9. 配置方法

#### 设置CDN域名 - `setCdnUrl`

```php
QiNiuFacade::setCdnUrl('https://new-cdn-domain.com/');
```

#### 设置上传凭证有效期 - `setExpires`

```php
QiNiuFacade::setExpires(3600); // 1小时
```

#### 设置自定义返回内容 - `setReturnBody`

```php
QiNiuFacade::setReturnBody('{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(fname)"}');
```

---

## 完整示例

### 示例1: 基础文件上传

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 初始化配置
QiNiuFacade::init(
    'YOUR_QINIU_ACCESS_KEY',
    'YOUR_QINIU_SECRET_KEY',
    'https://cdn.example.com/'
);

try {
    // 上传文件
    $result = QiNiuFacade::uploadFile(
        '/var/www/uploads/photo.jpg',
        'my-bucket',
        'photos/2025/01/photo.jpg',
        'image/jpeg'
    );

    echo "上传成功!" . PHP_EOL;
    echo "文件key: " . $result['key'] . PHP_EOL;
    echo "文件hash: " . $result['hash'] . PHP_EOL;
    echo "文件大小: " . $result['fsize'] . " 字节" . PHP_EOL;

    // 获取下载链接(私有空间)
    $url = QiNiuFacade::getPrivateFileUrl($result['key']);
    echo "下载链接: " . $url . PHP_EOL;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "上传失败: " . $e->getMessage();
}
```

### 示例2: 使用服务类(非静态)

```php
<?php

use YouHuJun\Tool\App\Service\V1\Store\QiNiuFacadeService;

// 创建服务实例
$qiNiuService = new QiNiuFacadeService(
    'YOUR_QINIU_ACCESS_KEY',
    'YOUR_QINIU_SECRET_KEY',
    'https://cdn.example.com/',
    7200
);

try {
    // 上传文件
    $result = $qiNiuService->uploadFile(
        '/var/www/uploads/photo.jpg',
        'my-bucket',
        'photos/2025/01/photo.jpg'
    );

    echo "上传成功!" . PHP_EOL;

    // 获取文件信息
    $fileInfo = $qiNiuService->getFileInfo('my-bucket', $result['key']);
    echo "文件大小: " . $fileInfo['fsize'] . " 字节" . PHP_EOL;

} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    echo "操作失败: " . $e->getMessage();
}
```

### 示例3: 用户头像上传

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

/**
 * 上传用户头像
 */
function uploadUserAvatar(int $userId, string $imagePath): ?string
{
    try {
        // 生成保存路径
        $savePath = 'avatars/' . date('Y/m') . '/' . $userId . '.jpg';

        // 上传文件
        $result = QiNiuFacade::uploadFile(
            $imagePath,
            'user-avatars',
            $savePath,
            'image/jpeg'
        );

        return $result['key'];

    } catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
        error_log("用户头像上传失败: " . $e->getMessage());
        return null;
    }
}

// 使用示例
$userId = 12345;
$imagePath = '/tmp/user_avatar.jpg';

$fileKey = uploadUserAvatar($userId, $imagePath);

if ($fileKey) {
    $publicUrl = QiNiuFacade::getPublicFileUrl($fileKey);
    echo "头像上传成功: " . $publicUrl;
} else {
    echo "头像上传失败";
}
```

### 示例4: 批量上传

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 初始化配置
QiNiuFacade::init(
    'YOUR_QINIU_ACCESS_KEY',
    'YOUR_QINIU_SECRET_KEY',
    'https://cdn.example.com/'
);

// 批量上传文件
$files = [
    '/path/to/file1.jpg',
    '/path/to/file2.jpg',
    '/path/to/file3.jpg'
];

$uploadedFiles = [];

foreach ($files as $index => $filePath) {
    try {
        $savePath = 'batch/' . date('YmdHis') . '_' . ($index + 1) . '.jpg';
        $result = QiNiuFacade::uploadFile($filePath, 'my-bucket', $savePath);
        $uploadedFiles[] = [
            'local_path' => $filePath,
            'remote_key' => $result['key'],
            'url' => QiNiuFacade::getPublicFileUrl($result['key'])
        ];
        echo "上传成功: " . $filePath . PHP_EOL;
    } catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
        echo "上传失败: " . $filePath . ", 错误: " . $e->getMessage() . PHP_EOL;
    }
}

print_r($uploadedFiles);
```

---

## 错误处理

所有方法在失败时都会抛出 `CommonException` 异常,建议使用 try-catch 捕获。

### 常见错误码

| 错误码 | 错误信息 | 说明 |
|--------|----------|------|
| 92000 | QiNiuAccessKeyError | 七牛云AccessKey未设置或为空 |
| 92010 | QiNiuSecretKeyError | 七牛云SecretKey未设置或为空 |
| 92020 | QiNiuBucketError | 七牛云存储空间名称未设置或为空 |
| 92030 | QiNiuCdnUrlError | 七牛云CDN域名未设置 |
| 92040 | QiNiuFileNotFoundError | 七牛云上传文件不存在 |
| 92050 | QiNiuFilePathEmpty | 七牛云文件路径为空 |
| 92060 | QiNiuUploadFileError | 七牛云上传文件失败 |
| 92070 | QiNiuUploadDataEmpty | 七牛云上传数据为空 |
| 92080 | QiNiuUploadDataError | 七牛云上传数据失败 |
| 92090 | QiNiuDeleteFileError | 七牛云删除文件失败 |
| 92100 | QiNiuGetFileInfoError | 七牛云获取文件信息失败 |
| 92110 | QiNiuNotInitialized | 七牛云服务未初始化 |

### 错误处理示例

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

try {
    $result = QiNiuFacade::uploadFile('/path/to/file.jpg', 'my-bucket', 'test.jpg');
} catch (\YouHuJun\Tool\App\Exceptions\CommonException $e) {
    $errorCode = $e->getCode();
    $errorMsg = $e->getMessage();

    switch ($errorCode) {
        case 92000:
            // AccessKey配置错误
            break;
        case 92040:
            // 本地文件不存在
            break;
        case 92060:
            // 上传失败,可能是网络问题或存储空间配置问题
            break;
        default:
            // 其他错误
            break;
    }
}
```

---

## 注意事项

### 1. SDK依赖

本服务依赖七牛云官方SDK,使用前请确保已安装:

```bash
composer require qiniu/php-sdk
```

### 2. 配置管理

- AccessKey 和 SecretKey 是敏感信息,请妥善保管
- 不要将密钥硬编码在前端代码中
- 建议使用环境变量或配置文件管理密钥

### 3. 文件路径

- `savePath` 是文件在七牛云中的key,不是本地路径
- 建议使用有意义的目录结构,如 `images/2025/01/`
- 不同操作系统路径分隔符不同,建议使用 `/`

### 4. 上传凭证

- 上传凭证有有效期,默认7200秒(2小时)
- 有效期过后需要重新生成凭证
- 建议根据业务需求设置合适的有效期

### 5. CDN域名

- CDN域名需要在七牛云控制台绑定
- 私有空间和公有空间的访问方式不同
- 确保域名已备案(大陆地区)

---

## 常见问题

### Q: 如何获取AccessKey和SecretKey?

A: 登录七牛云控制台,进入"个人中心" > "密钥管理"即可查看。

### Q: 上传文件大小有限制吗?

A: 七牛云单文件上传限制为10GB以内,大文件建议使用分片上传。

### Q: 如何设置私有空间和公有空间?

A: 在七牛云控制台的存储空间设置中修改访问权限。

### Q: 上传凭证有效期设置多少合适?

A: 建议:
- 服务端上传: 使用较长有效期,如7200秒
- 客户端上传: 使用较短有效期,如3600秒

### Q: 如何处理上传失败?

A: 建议:
1. 检查网络连接
2. 验证AccessKey和SecretKey是否正确
3. 确认存储空间名称正确
4. 检查存储空间是否有足够的空间

---

## 迁移指南

如果您之前使用的是 Laravel Cache 读取配置,可以按以下方式迁移:

### 旧代码

```php
$qiNiuService = new QiNiuFacadeService();
$result = $qiNiuService->uploadFile($filePath, $savePath);
```

### 新代码

```php
// 方式1: 使用静态门面(推荐)
QiNiuFacade::init($accessKey, $secretKey, $cdnUrl);
$result = QiNiuFacade::uploadFile($filePath, $bucket, $savePath);

// 方式2: 使用服务类
$qiNiuService = new QiNiuFacadeService($accessKey, $secretKey, $cdnUrl);
$result = $qiNiuService->uploadFile($filePath, $bucket, $savePath);
```

### 配置对比

| 配置项 | 旧代码 | 新代码 |
|--------|--------|--------|
| AccessKey | Cache::get('qiniu.accessKey') | 构造函数参数或init()方法 |
| SecretKey | Cache::get('qiniu.secretKey') | 构造函数参数或init()方法 |
| Bucket | Cache::get('qiniu.bucket.default') | uploadFile()方法参数 |
| CDN URL | Cache::get('qiniu.cdn_url') | 构造函数参数或setCdnUrl() |

# 二维码生成服务使用文档

## 概述

`QrcodeFacade` 是二维码生成服务的静态门面类,提供了灵活的二维码生成功能,支持多种输出模式和自定义配置。使用静态代理模式调用,简单便捷。

## 配置参数

### 1. `$config` - 配置参数

```php
$config = [
    'logoPath' => '/path/to/logo.png',           // 可选: Logo图片路径
    'noticeInfo' => '扫码关注',                   // 可选: 二维码下方提示文字,默认"二维码"
    'qrcodePath' => '/path/to/save/qrcode.png',  // 可选: 保存路径(mode=1时必填)
    'size' => 300,                               // 可选: 二维码尺寸,默认300
    'margin' => 10,                              // 可选: 二维码边距,默认10
    'logoResizeToWidth' => 50,                   // 可选: Logo宽度,默认50
];
```

### 2. `$params` - 二维码参数

```php
$params = [
    'data' => 'https://example.com',            // 必填: 二维码内容(URL或文本)
];
```

### 3. `$mode` - 输出模式

- `1` - 保存到文件,返回保存路径
- `2` - 直接输出到浏览器(设置Content-Type并输出图片)
- `3` - 生成Data URI,返回可在`<img>`标签中使用的base64编码字符串

## 使用示例

### 基本用法 - 生成简单二维码

```php
use YouHuJun\Tool\App\Facade\V1\Qrcode\QrcodeFacade;

$config = [];
$params = [
    'data' => 'https://example.com',
];

// 生成Data URI,用于前端显示
$result = QrcodeFacade::makeQrcode($config, $params, 3);

echo '<img src="' . $result . '" alt="二维码">';
```

### 保存二维码到文件

```php
$config = [
    'qrcodePath' => '/var/www/storage/qrcode.png',
];

$params = [
    'data' => 'https://example.com/share?code=abc123',
];

// 保存到文件,返回保存路径
$savePath = QrcodeFacade::makeQrcode($config, $params, 1);

echo "二维码已保存到: " . $savePath;
```

### 直接输出二维码

```php
$params = [
    'data' => 'https://example.com',
];

// 直接输出到浏览器
QrcodeFacade::makeQrcode([], $params, 2);
```

### 生成带Logo的二维码

```php
$config = [
    'logoPath' => '/path/to/logo.png',
    'logoResizeToWidth' => 80,
    'size' => 400,
    'noticeInfo' => '扫码关注公众号',
];

$params = [
    'data' => 'https://example.com',
];

// 生成Data URI
$result = QrcodeFacade::makeQrcode($config, $params, 3);

echo '<img src="' . $result . '" alt="带Logo的二维码">';
```

### 用户邀请码二维码

```php
// 用户邀请码示例
$userId = 12345;
$inviteCode = 'ABC123';
$shareUrl = 'https://example.com/share?code=' . $inviteCode;

$config = [
    'logoPath' => '/path/to/logo.png',
    'noticeInfo' => '扫码邀请好友',
    'qrcodePath' => '/var/www/storage/user/qrcode/' . $userId . '.png',
    'size' => 300,
];

$params = [
    'data' => $shareUrl,
];

// 保存到文件
$savePath = QrcodeFacade::makeQrcode($config, $params, 1);

echo "用户邀请码二维码已保存: " . $savePath;
```

### 自定义二维码样式

```php
$config = [
    'logoPath' => '/path/to/logo.png',
    'noticeInfo' => '扫码关注',
    'size' => 500,              // 更大的尺寸
    'margin' => 20,             // 更大的边距
    'logoResizeToWidth' => 100, // 更大的Logo
];

$params = [
    'data' => 'https://example.com',
];

$result = QrcodeFacade::makeQrcode($config, $params, 3);
```

### 生成文本二维码

```php
$params = [
    'data' => '这是一段文本内容,扫描即可查看',
];

$config = [
    'noticeInfo' => '扫描查看文本',
];

$result = QrcodeFacade::makeQrcode($config, $params, 3);
```

## 返回值

根据`$mode`不同,返回值不同:

- **mode = 1**: 返回二维码保存的文件路径(字符串)
- **mode = 2**: 直接输出到浏览器,无返回值
- **mode = 3**: 返回Data URI字符串,格式如 `data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...`

## 错误处理

服务会抛出 `CommonException`,包含以下错误码:

| 错误码 | Code | 说明 |
|--------|------|------|
| QrcodeDataRequired | 80000 | 二维码数据必填 |
| QrcodeSavePathRequired | 80010 | 二维码保存路径必填(mode=1时) |
| QrcodeSaveError | 80020 | 二维码保存失败 |
| QrcodeModeError | 80030 | 二维码输出模式错误 |

### 错误处理示例

```php
use YouHuJun\Tool\App\Exceptions\CommonException;
use YouHuJun\Tool\App\Facade\V1\Qrcode\QrcodeFacade;

try {
    $result = QrcodeFacade::makeQrcode($config, $params, 3);
    echo '<img src="' . $result . '" alt="二维码">';
} catch (CommonException $e) {
    echo '错误码: ' . $e->getCode() . PHP_EOL;
    echo '错误信息: ' . $e->getMessage() . PHP_EOL;
}
```

## 注意事项

1. **Logo图片**: Logo图片路径必须是绝对路径,如果使用相对路径需要先转换为绝对路径
2. **保存路径**: 使用mode=1保存文件时,需要确保目标目录有写入权限
3. **输出模式**: mode=2会直接输出到浏览器并终止脚本执行,适合用于API接口或独立页面
4. **Data URI**: mode=3返回的Data URI可以直接嵌入HTML的`<img>`标签的`src`属性中
5. **错误容错**: 二维码使用High级别的错误纠正,即使有30%的损坏仍可扫描
6. **文本编码**: 默认使用UTF-8编码,支持中文和特殊字符
7. **文件权限**: 确保存放二维码的目录有写入权限
8. **静态调用**: 使用 `QrcodeFacade::makeQrcode()` 静态方法调用,无需实例化

## 高级用法

### 批量生成二维码

```php
use YouHuJun\Tool\App\Facade\V1\Qrcode\QrcodeFacade;
use YouHuJun\Tool\App\Exceptions\CommonException;

$users = [
    ['id' => 1, 'inviteCode' => 'ABC123'],
    ['id' => 2, 'inviteCode' => 'DEF456'],
    ['id' => 3, 'inviteCode' => 'GHI789'],
];

$baseConfig = [
    'logoPath' => '/path/to/logo.png',
    'noticeInfo' => '扫码邀请',
    'size' => 300,
];

foreach ($users as $user) {
    $config = array_merge($baseConfig, [
        'qrcodePath' => '/var/www/storage/user/qrcode/' . $user['id'] . '.png',
    ]);

    $params = [
        'data' => 'https://example.com/share?code=' . $user['inviteCode'],
    ];

    try {
        $savePath = QrcodeFacade::makeQrcode($config, $params, 1);
        echo "用户 {$user['id']} 的二维码已保存: " . $savePath . "\n";
    } catch (CommonException $e) {
        echo "用户 {$user['id']} 的二维码生成失败: " . $e->getMessage() . "\n";
    }
}
```

### 动态生成二维码接口

```php
use YouHuJun\Tool\App\Facade\V1\Qrcode\QrcodeFacade;
use YouHuJun\Tool\App\Exceptions\CommonException;

// 在Controller或路由中使用
header('Content-Type: application/json');

try {
    $params = [
        'data' => $_GET['url'] ?? '',
    ];

    $config = [
        'logoPath' => '/path/to/logo.png',
        'noticeInfo' => '扫码访问',
        'size' => 400,
    ];

    // 生成Data URI返回给前端
    $dataUri = QrcodeFacade::makeQrcode($config, $params, 3);

    echo json_encode([
        'code' => 0,
        'data' => $dataUri,
    ]);
} catch (CommonException $e) {
    echo json_encode([
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ]);
}
```

## 相关依赖

本服务使用 `endroid/qr-code` 包生成二维码,已在composer.json中配置:

```json
{
    "require": {
        "endroid/qr-code": "*"
    }
}
```

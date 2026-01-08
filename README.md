# youhujun/php-tool-youhujun

<div align="center">
   <a target="_blank" href="https://www.youhu.club/">📑 阅读文档</a>|  <a target="_blank" href="https://www.youhujun.com/">🌐 参考博客</a> 
</div>

## 项目简介

[php-tool-youhujun](https://gitcode.com/youhujun/) 是为了提高php开发效率,无需关乎基础通用功能的实现,将精力聚焦于业务逻辑的开发而催生的.

## 项目功能

本工具包提供了丰富的 PHP 开发常用功能,涵盖以下模块:

### 📊 数据处理
- **Excel**: Excel 文件的导入和导出功能

### 📅 日期时间
- **日历**: 公历与农历互转

### 🏷️ 二维码
- **二维码生成**: 支持多种格式的二维码生成

### 📨 消息服务
- **腾讯云短信**: 短信发送功能

### 🗺️ 地图服务
- **腾讯地图**: 地图相关功能

### 🔐 微信生态
- **微信小程序**: 微信小程序登录
- **微信公众号**: 微信公众号网页授权
- **微信支付**: 微信 JSAPI 支付、支付回调解密

### 🎵 抖音生态
- **抖音登录**: 抖音小程序和小游戏登录

### 🔒 加密解密
- **AES 加密**: AES 加密解密功能
- **RSA 加密**: RSA 公钥加密私钥解密功能

### ☁️ 云存储
- **七牛云存储**: 文件上传、删除、获取文件信息等

## 安装

```bash
composer require youhujun/php-tool-youhujun
```

### 环境要求

- PHP >= 8.0.2
- 扩展: openssl, json, gd

## 使用

### Excel 导入导出

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

// 导出
ExcelFacade::exportExcelData($columns, $data, $title, $saveDirectory);

// 导入
ExcelFacade::initReadExcel($this->tempFile);
ExcelFacade::setWorkSheet(0);
$result = ExcelFacade::getDataByRow();

// 移除表头后再进行断言，这更符合真实业务场景
array_shift($result);

//数据库操作
```

### 日历转换

```php
use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

// 农历转公历
$solarDate = CalendarFacade::lunarToSolar($lunarYear, $lunarMonth, $lunarDay, $isLeapMonth);

// 公历转农历
$lunarDate = CalendarFacade::solarToLunar($solarYear, $solarMonth, $solarDay);
```

### 二维码生成

```php
use YouHuJun\Tool\App\Facade\V1\Qrcode\QrcodeFacade;

// 生成二维码
$qrCode = QrcodeFacade::generate('https://example.com', $size = 300);
$qrCode->save('qrcode.png');
```

### 微信小程序登录

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\MiniProgram\WechatMiniProgramFacade;

// 获取 OpenID
$result = WechatMiniProgramFacade::getOpenIdByCode($code, $appid, $appSecret);
```

### 抖音登录

```php
use YouHuJun\Tool\App\Facade\V1\DouYin\Login\DouYinLoginFacade;

// 抖音小程序登录
$result = DouYinLoginFacade::getOpenIdByCodeWithMiniProgram($code, $anonymousCode, $appid, $appSecret);

// 抖音小游戏登录
$result = DouYinLoginFacade::getOpenIdByCodeWithMiniGame($code, $anonymousCode, $appid, $appSecret);
```

### AES 加密解密

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\AESFacade;

// 加密
$encrypted = AESFacade::encrypt('hello world', 'your-key');

// 解密
$decrypted = AESFacade::decrypt($encrypted, 'your-key');
```

### RSA 加密解密

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Secret\RSAFacade;

// 公钥加密
$encrypted = RSAFacade::encrypt('hello world', $publicKey);

// 私钥解密
$decrypted = RSAFacade::decrypt($encrypted, $privateKey);
```

### 七牛云存储

```php
use YouHuJun\Tool\App\Facade\V1\Store\QiNiuFacade;

// 初始化配置
QiNiuFacade::init($accessKey, $secretKey, $cdnUrl, $bucket);

// 上传文件
$result = QiNiuFacade::uploadFile($filePath, $savePath);

// 获取私有文件链接
$url = QiNiuFacade::getPrivateFileUrl($savePath);

// 获取公有文件链接
$url = QiNiuFacade::getPublicFileUrl($savePath);
```

- 导出

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

ExcelFacade::exportExcelData($columns, $data, $title, $saveDirectory);
```
- 导入

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

ExcelFacade::initReadExcel($this->tempFile);
ExcelFacade::setWorkSheet(0);
$result = ExcelFacade::getDataByRow();

// 移除表头后再进行断言，这更符合真实业务场景
array_shift($result);

//数据库操作
```

**更多文档请查看[文档地址](https://www.youhu.club/)**

## 测试

### 安装测试依赖
```bash
composer install --dev
```

### 运行测试

- 单独测试 Excel

```bash
composer test --testsuite Excel
```

- 单独测试 Calendar

```bash
composer test --testsuite Calendar
```

- 全部测试

```bash
composer test
```

> 注意: 部分功能模块(如微信支付、短信服务等)需要第三方服务的 Key 或 AppID,因此暂时不包含在自动测试中。

## 变更日志

查看详细的版本变更记录,请访问 [CHANGELOG.md](CHANGELOG.md)

## 文档

**更多详细文档请查看[文档地址](https://www.youhu.club/)**

## 许可证

MIT License
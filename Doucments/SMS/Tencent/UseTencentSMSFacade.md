# 腾讯云短信服务使用文档

## 概述

`TencentSMSFacade` 是腾讯云短信服务的静态门面类,提供了发送短信验证码的功能。使用静态代理模式调用,简单便捷。

## 配置参数

### 基础配置

发送短信时需要传入两个参数数组:

#### 1. `$config` - 腾讯云基础配置

```php
$config = [
    'secretId' => 'your_secret_id',        // 腾讯云SecretId
    'secretKey' => 'your_secret_key',      // 腾讯云SecretKey
    'smsApConfig' => 'ap-guangzhou',       // 地域配置,如: ap-guangzhou
    'smsSdkAppId' => '1400787878',        // 短信应用ID
    'signName' => '你的签名',               // 短信签名
    'templateId' => '1882890',             // 短信模板ID
    'phonePre' => '+86',                   // 手机号前缀
];
```

#### 2. `$smsParam` - 短信发送参数

```php
$smsParam = [
    'smsContent' => ['1234'],              // 短信模板参数数组
    'phoneNumber' => ['13800138000'],      // 手机号数组,支持多个
    // 以下参数可选,如果未提供则使用$config中的值
    'smsApConfig' => 'ap-guangzhou',       // 可选: 覆盖config中的地域配置
    'smsSdkAppId' => '1400787878',        // 可选: 覆盖config中的应用ID
    'signName' => '你的签名',               // 可选: 覆盖config中的签名
    'templateId' => '1882890',             // 可选: 覆盖config中的模板ID
    'phonePre' => '+86',                   // 可选: 覆盖config中的手机号前缀
    'curlMethods' => 'POST',               // 可选: HTTP请求方法,默认POST
    'signMethods' => 'TC3-HMAC-SHA256',    // 可选: 签名算法,默认TC3-HMAC-SHA256
];
```

## 使用示例

### 基本用法

```php
use YouHuJun\Tool\App\Facade\V1\SMS\Tencent\TencentSMSFacade;

// 配置腾讯云参数
$config = [
    'secretId' => 'YOUR_TENCENT_SECRET_ID',   // 替换为你的腾讯云SecretId
    'secretKey' => 'YOUR_TENCENT_SECRET_KEY', // 替换为你的腾讯云SecretKey
    'smsApConfig' => 'ap-guangzhou',
    'smsSdkAppId' => '1400787878',
    'signName' => '腾讯云',
    'templateId' => '1882890',
    'phonePre' => '+86',
];

// 短信发送参数
$smsParam = [
    'smsContent' => ['1234'],              // 模板中的验证码
    'phoneNumber' => ['13800138000'],     // 发送的手机号
];

// 发送短信
$result = TencentSMSFacade::sendSms($config, $smsParam);

if ($result === 1) {
    echo '短信发送成功';
} else {
    echo '短信发送失败';
}
```

### 发送多个手机号

```php
$smsParam = [
    'smsContent' => ['1234'],
    'phoneNumber' => [
        '13800138000',
        '13900139000',
        '13700137000'
    ],
];

$result = TencentSMSFacade::sendSms($config, $smsParam);
```

### 动态覆盖配置

```php
// 基础配置
$config = [
    'secretId' => 'YOUR_TENCENT_SECRET_ID',   // 替换为你的腾讯云SecretId
    'secretKey' => 'YOUR_TENCENT_SECRET_KEY', // 替换为你的腾讯云SecretKey
    'smsApConfig' => 'ap-guangzhou',
    'smsSdkAppId' => '1400787878',
    'signName' => '腾讯云',
    'templateId' => '1882890',
    'phonePre' => '+86',
];

// 发送登录验证码,使用默认模板ID
$smsParam1 = [
    'smsContent' => ['1234'],
    'phoneNumber' => ['13800138000'],
];

// 发送注册验证码,覆盖模板ID
$smsParam2 = [
    'smsContent' => ['5678'],
    'phoneNumber' => ['13900139000'],
    'templateId' => '1882891',  // 覆盖默认模板ID
];

$result1 = TencentSMSFacade::sendSms($config, $smsParam1);
$result2 = TencentSMSFacade::sendSms($config, $smsParam2);
```

## 返回值

- `1` - 短信发送成功
- `0` - 短信发送失败

## 错误处理

服务会抛出 `CommonException`,包含以下错误码:

| 错误码 | Code | 说明 |
|--------|------|------|
| TencentCloudSecretIdError | 70000 | 腾讯云SecretId未设置 |
| TencentCloudSecretKeyError | 70010 | 腾讯云SecretKey未设置 |
| TencentCloudSmsApConfigError | 70020 | 腾讯云短信地域配置未设置 |
| TencentCloudSmsSdkAppIdError | 70030 | 腾讯云短信SdkAppId未设置 |
| TencentCloudSmsSignNameError | 70040 | 腾讯云短信签名未设置 |
| TencentCloudSmsTemplateIdError | 70050 | 腾讯云短信模板ID未设置 |
| TencentCloudSmsPhonePreError | 70060 | 腾讯云短信手机号前缀未设置 |
| TencentCloudSmsContentError | 70070 | 腾讯云短信内容参数错误 |
| TencentCloudSmsPhoneNumberError | 70080 | 腾讯云短信手机号参数错误 |
| TencentCloudSmsSendError | 70090 | 腾讯云短信发送失败 |
| TencentCloudSmsError | 70100 | 腾讯云短信服务异常 |

### 错误处理示例

```php
use YouHuJun\Tool\App\Exceptions\CommonException;
use YouHuJun\Tool\App\Facade\V1\SMS\Tencent\TencentSMSFacade;

try {
    $result = TencentSMSFacade::sendSms($config, $smsParam);
    echo '发送结果: ' . ($result === 1 ? '成功' : '失败');
} catch (CommonException $e) {
    echo '错误码: ' . $e->getCode() . PHP_EOL;
    echo '错误信息: ' . $e->getMessage() . PHP_EOL;
}
```

## 注意事项

1. **配置参数优先级**: `$smsParam` 中的可选参数会覆盖 `$config` 中对应的值
2. **手机号前缀**: 默认使用 `+86`,发送到国际手机号时需要修改
3. **模板参数**: `$smsContent` 数组中的元素个数必须与短信模板中的变量个数一致
4. **多手机号发送**: 支持一次发送最多200个手机号
5. **地域配置**: 常见地域有 `ap-guangzhou`(广州)、`ap-beijing`(北京)、`ap-shanghai`(上海)
6. **静态调用**: 使用 `TencentSMSFacade::sendSms()` 静态方法调用,无需实例化

## 相关链接

- 腾讯云短信控制台: https://console.cloud.tencent.com/smsv2
- 腾讯云短信SDK文档: https://cloud.tencent.com/document/product/382/43193


# 微信JSAPI支付回调解密使用文档

## 简介

`WechatPayDecryptFacade` 提供了微信支付回调数据的验证签名和解密功能，基于微信支付官方SDK。

## 安装依赖

确保已安装微信支付官方SDK：

```bash
composer require wechatpay/wechatpay
```

## 配置参数

### 配置数组

```php
$config = [
    'apiv3Key' => 'YOUR_WECHAT_PAY_API_V3_KEY',                              // 微信支付APIv3密钥(32位)
    'wechatpayCertificatePath' => '/path/to/your/wechatpay.pem'    // 微信支付平台证书文件路径
];
```

## 使用方法

### 验证签名并解密回调数据

处理微信支付回调时，验证签名并解密回调数据：

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\Pay\JSAPI\WechatPayDecryptFacade;

$config = [
    'apiv3Key' => 'YOUR_WECHAT_PAY_API_V3_KEY',
    'wechatpayCertificatePath' => '/path/to/your/wechatpay.pem'
];

// 获取回调数据
$notifyData = [
    'wechatpay_signature' => $_SERVER['HTTP_WECHATPAY_SIGNATURE'],
    'wechatpay_timestamp' => $_SERVER['HTTP_WECHATPAY_TIMESTAMP'],
    'wechatpay_serial' => $_SERVER['HTTP_WECHATPAY_SERIAL'],
    'wechatpay_nonce' => $_SERVER['HTTP_WECHATPAY_NONCE'],
    'body' => file_get_contents('php://input')
];

try {
    $decryptedData = WechatPayDecryptFacade::decryptData($config, $notifyData);

    // 返回解密后的数据
    // [
    //     'appid' => 'YOUR_WECHAT_PAY_APPID',
    //     'mchid' => '1234567890',
    //     'out_trade_no' => 'ORDER202601070001',
    //     'transaction_id' => '4200001234567890',
    //     'trade_type' => 'JSAPI',
    //     'trade_state' => 'SUCCESS',
    //     'trade_state_desc' => '支付成功',
    //     'amount' => [
    //         'total' => 100,
    //         'payer_total' => 100
    //     ],
    //     'payer' => [
    //         'openid' => 'oYFqa5nW_bn2icDWNBi4xEXpRf5E'
    //     ]
    // ]

    // 处理支付成功逻辑
    $outTradeNo = $decryptedData['out_trade_no'];
    $transactionId = $decryptedData['transaction_id'];
    $tradeState = $decryptedData['trade_state'];

    if ($tradeState === 'SUCCESS') {
        // 支付成功，更新订单状态
        // ...
    }

    // 返回成功响应给微信
    echo json_encode(['code' => 'SUCCESS', 'message' => '成功']);

} catch (\Exception $e) {
    // 返回失败响应给微信
    echo json_encode(['code' => 'FAIL', 'message' => '失败']);
}
```

### Laravel 框架中获取回调数据

如果你的项目使用Laravel框架：

```php
use Illuminate\Http\Request;

public function payNotify(Request $request)
{
    $config = [
        'apiv3Key' => config('wechat.pay.api_v3_key'),
        'wechatpayCertificatePath' => storage_path('app/public/wechatpay.pem')
    ];

    $notifyData = [
        'wechatpay_signature' => $request->header('Wechatpay-Signature'),
        'wechatpay_timestamp' => $request->header('Wechatpay-Timestamp'),
        'wechatpay_serial' => $request->header('Wechatpay-Serial'),
        'wechatpay_nonce' => $request->header('Wechatpay-Nonce'),
        'body' => $request->getContent()
    ];

    $decryptedData = WechatPayDecryptFacade::decryptData($config, $notifyData);

    // 处理逻辑...
}
```

### 纯PHP框架中获取回调数据

```php
$headers = getallheaders();

$config = [
    'apiv3Key' => 'YOUR_WECHAT_PAY_API_V3_KEY',
    'wechatpayCertificatePath' => '/path/to/your/wechatpay.pem'
];

$notifyData = [
    'wechatpay_signature' => $headers['Wechatpay-Signature'] ?? '',
    'wechatpay_timestamp' => $headers['Wechatpay-Timestamp'] ?? '',
    'wechatpay_serial' => $headers['Wechatpay-Serial'] ?? '',
    'wechatpay_nonce' => $headers['Wechatpay-Nonce'] ?? '',
    'body' => file_get_contents('php://input')
];

$decryptedData = WechatPayDecryptFacade::decryptData($config, $notifyData);
```

### ⚠️ 重要提示：数组键名必须严格匹配

**请务必注意：** `$notifyData` 数组的键名必须与 `WechatPayDecryptFacade` 解析的键名完全一致，**不能添加任何前缀或后缀**。

**正确的格式：**
```php
$inWechatpaySignature = $request->header('Wechatpay-Signature');
$inWechatpayTimestamp = $request->header('Wechatpay-Timestamp');
$inWechatpaySerial = $request->header('Wechatpay-Serial');
$inWechatpayNonce = $request->header('Wechatpay-Nonce');
$inBody = file_get_contents('php://input');

$notifyData = [
    'wechatpay_signature' => $inWechatpaySignature,      // ✅ 正确
    'wechatpay_timestamp' => $inWechatpayTimestamp,      // ✅ 正确
    'wechatpay_serial' => $inWechatpaySerial,          // ✅ 正确
    'wechatpay_nonce' => $inWechatpayNonce,            // ✅ 正确
    'body' => $inBody                                  // ✅ 正确
];
```

**常见的错误格式：**
```php
$notifyData = [
    '$inWechatpaySignature' => $inWechatpaySignature,    // ❌ 错误：多了 $ 前缀
    'inWechatpaySignature' => $inWechatpaySignature,     // ❌ 错误：多了 in 前缀
    'WechatpaySignature' => $inWechatpaySignature,       // ❌ 错误：大小写不对
];
```

**原因：** 如果键名不匹配，PHP 解构时变量会是 `null`，导致签名验证失败，最终抛出解密异常。


### 设置时间偏移量

默认允许5分钟的时间偏移，可以自定义：

```php
// 设置允许的时间偏移量为10分钟
WechatPayDecryptFacade::setTimeOffset(600);

// 或者先获取实例再设置
$decryptService = new WechatPayDecryptFacadeService();
$decryptService->setTimeOffset(600);
```

## 异常处理

解密失败时会抛出 `CommonException` 异常：

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

try {
    $decryptedData = WechatPayDecryptFacade::decryptData($config, $notifyData);
} catch (CommonException $e) {
    $error = $e->getErrorResponse();
    // [
    //     'code' => 51070,
    //     'error' => 'WechatApiV3KKeyNotExistsError',
    //     'msg' => '微信APIv3密钥未设置'
    // ]

    // 记录日志
    error_log('解密失败: ' . $error['msg']);

    // 返回失败响应
    echo json_encode(['code' => 'FAIL', 'message' => '解密失败']);
}
```

## 错误码说明

| 错误码 | 错误标识 | 说明 |
|--------|----------|------|
| 51030 | WechatMerchantWechatpayCertificateError | 微信支付平台证书文件不存在或无法读取 |
| 51070 | WechatApiV3KKeyNotExistsError | 微信APIv3密钥未设置 |
| 51060 | PrePayOrderByWechatJsError | 验证签名失败或数据格式错误 |

## 注意事项

1. **证书文件路径**：证书文件路径必须是绝对路径
2. **APIv3密钥**：在微信支付商户平台设置，必须是32位字符
3. **时间偏移**：默认允许5分钟的时间偏移，可根据需要调整
4. **签名验证**：服务会自动验证签名和时间，验证失败会抛出异常
5. **回调响应**：处理完成后必须返回JSON格式的响应给微信

## 完整示例：支付回调处理

```php
<?php

require_once 'vendor/autoload.php';

use YouHuJun\Tool\App\Facade\V1\Wechat\Pay\JSAPI\WechatPayDecryptFacade;
use YouHuJun\Tool\App\Exceptions\CommonException;

// 配置
$config = [
    'apiv3Key' => 'YOUR_WECHAT_PAY_API_V3_KEY',
    'wechatpayCertificatePath' => __DIR__ . '/certs/wechatpay.pem'
];

// 获取回调数据
$headers = getallheaders();
$notifyData = [
    'wechatpay_signature' => $headers['Wechatpay-Signature'] ?? '',
    'wechatpay_timestamp' => $headers['Wechatpay-Timestamp'] ?? '',
    'wechatpay_serial' => $headers['Wechatpay-Serial'] ?? '',
    'wechatpay_nonce' => $headers['Wechatpay-Nonce'] ?? '',
    'body' => file_get_contents('php://input')
];

try {
    // 解密回调数据
    $decryptedData = WechatPayDecryptFacade::decryptData($config, $notifyData);

    // 打印解密后的数据(调试用)
    error_log('解密数据: ' . json_encode($decryptedData, JSON_UNESCAPED_UNICODE));

    // 检查交易状态
    if ($decryptedData['trade_state'] !== 'SUCCESS') {
        echo json_encode(['code' => 'SUCCESS', 'message' => '已接收']);
        exit;
    }

    // 获取订单信息
    $outTradeNo = $decryptedData['out_trade_no'];
    $transactionId = $decryptedData['transaction_id'];
    $totalAmount = $decryptedData['amount']['total'];

    // TODO: 更新订单状态
    // updateOrderStatus($outTradeNo, 'paid', $transactionId, $totalAmount);

    // 返回成功响应
    echo json_encode(['code' => 'SUCCESS', 'message' => '成功']);

} catch (CommonException $e) {
    $error = $e->getErrorResponse();
    error_log('处理失败: ' . json_encode($error));

    // 返回失败响应
    echo json_encode(['code' => 'FAIL', 'message' => '失败']);
}
```

## 支付状态说明

解密后的 `trade_state` 字段可能的值：

| 状态值 | 说明 |
|--------|------|
| SUCCESS | 支付成功 |
| REFUND | 转入退款 |
| NOTPAY | 未支付 |
| CLOSED | 已关闭 |
| REVOKED | 已撤销(付款码支付) |
| USERPAYING | 用户支付中 |
| PAYERROR | 支付失败(其他原因，如银行返回失败) |

## 与下单接口配合使用

完整的支付流程：

```php
// 1. 用户下单
$config = [...];
$orderData = [...];
$result = WechatPayByJSAPIFacade::prePayOrder($config, $orderData);

// 2. 前端调起支付

// 3. 微信回调通知
$config = [
    'apiv3Key' => 'YOUR_WECHAT_PAY_API_V3_KEY',
    'wechatpayCertificatePath' => '/path/to/your/wechatpay.pem'
];
$notifyData = [...];
$decryptedData = WechatPayDecryptFacade::decryptData($config, $notifyData);

// 4. 处理业务逻辑

// 5. 返回响应给微信
```

## 官方文档

- [微信支付API文档 - 支付结果通知](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_5_5.shtml)
- [微信支付API文档 - 通知签名验证](https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay4_0.shtml)
- [微信支付SDK](https://github.com/wechatpay-apiv3/wechatpay-guzzle-middleware)

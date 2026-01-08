# 微信JSAPI支付使用文档

## 简介

`WechatPayByJSAPIFacade` 提供了微信JSAPI支付的完整功能封装，基于微信支付官方SDK，支持下单、查询、关闭、退款等操作。

## 安装依赖

确保已安装微信支付官方SDK：

```bash
composer require wechatpay/wechatpay
composer require guzzlehttp/guzzle
```

## 配置参数

### 支付配置数组

```php
$config = [
    'merchantId' => '商户号',                          // 微信支付商户号
    'merchantSerialNumber' => '证书序列号',             // 商户API证书序列号
    'merchantPrivateKeyPath' => '/path/to/your/apiclient_key.pem',  // 商户私钥文件路径
    'wechatpayCertificatePath' => '/path/to/your/wechatpay.pem',     // 微信支付平台证书文件路径
    'officialAppid' => 'YOUR_WECHAT_OFFICIAL_APPID',              // 公众号AppId
    'notifyUrl' => 'https://yourdomain.com/notify'    // 支付回调通知地址
];
```

## 使用方法

### 1. JSAPI下单

发起JSAPI支付下单请求：

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\Pay\JSAPI\WechatPayByJSAPIFacade;

$config = [
    'merchantId' => '1234567890',
    'merchantSerialNumber' => 'ABCDEF1234567890ABCDEF',
    'merchantPrivateKeyPath' => '/path/to/your/apiclient_key.pem',
    'wechatpayCertificatePath' => '/path/to/your/wechatpay.pem',
    'officialAppid' => 'YOUR_WECHAT_OFFICIAL_APPID',
    'notifyUrl' => 'https://yourdomain.com/pay/notify'
];

$orderData = [
    'description' => '商品描述',                     // 商品描述
    'out_trade_no' => 'ORDER202601070001',           // 商户订单号(6-32位)
    'amount' => [                                    // 订单金额
        'total' => 100,                              // 总金额(单位:分)
        'currency' => 'CNY'                           // 货币类型
    ],
    'payer' => [                                     // 支付者信息
        'openid' => 'oYFqa5nW_bn2icDWNBi4xEXpRf5E'   // 用户OpenID
    ],
    'attach' => '自定义数据',                        // 可选:附加数据
    'time_expire' => date('Y-m-d\TH:i:sP', time() + 3600)  // 可选:订单失效时间
];

$result = WechatPayByJSAPIFacade::prePayOrder($config, $orderData);

// 返回结果
// [
//     'prepay_id' => 'wx1916534311771708341ae662f3b2220000',
//     'appId' => 'YOUR_WECHAT_OFFICIAL_APPID',
//     'timeStamp' => '1704600000',
//     'nonceStr' => '5K8264ILTKCH16CQ2502SI8ZNMTM67VS',
//     'paySign' => 'oR9d8P....'
// ]
```

### 2. 查询订单

根据商户订单号查询订单状态：

```php
$outTradeNo = 'ORDER202601070001';

$orderInfo = WechatPayByJSAPIFacade::queryOrder($config, $outTradeNo);

// 返回订单信息数组
// [
//     'appid' => 'YOUR_WECHAT_OFFICIAL_APPID',
//     'mchid' => '1234567890',
//     'out_trade_no' => 'ORDER202601070001',
//     'transaction_id' => '4200001234567890',
//     'trade_type' => 'JSAPI',
//     'trade_state' => 'SUCCESS',
//     'trade_state_desc' => '支付成功',
//     'amount' => [...]
// ]
```

### 3. 关闭订单

关闭未支付的订单：

```php
$outTradeNo = 'ORDER202601070001';

$result = WechatPayByJSAPIFacade::closeOrder($config, $outTradeNo);

// 返回 true 表示关闭成功
```

### 4. 申请退款

对已支付订单发起退款：

```php
$refundData = [
    'out_trade_no' => 'ORDER202601070001',           // 原订单号
    'out_refund_no' => 'REFUND202601070001',         // 退款单号
    'reason' => '用户申请退款',                       // 退款原因
    'amount' => [                                    // 退款金额
        'refund' => 50,                              // 退款金额(单位:分)
        'total' => 100,                             // 原订单金额
        'currency' => 'CNY'
    ],
    'notify_url' => 'https://yourdomain.com/refund/notify'  // 可选:退款回调地址
];

$refundResult = WechatPayByJSAPIFacade::refund($config, $refundData);

// 返回退款结果
// [
//     'refund_id' => '50000000000000000000000000000000',
//     'out_refund_no' => 'REFUND202601070001',
//     'transaction_id' => '4200001234567890',
//     'status' => 'SUCCESS'
// ]
```

### 5. 查询退款

查询退款状态：

```php
$outRefundNo = 'REFUND202601070001';

$refundInfo = WechatPayByJSAPIFacade::queryRefund($config, $outRefundNo);

// 返回退款信息数组
// [
//     'refund_id' => '50000000000000000000000000000000',
//     'out_refund_no' => 'REFUND202601070001',
//     'transaction_id' => '4200001234567890',
//     'status' => 'SUCCESS',
//     'amount' => [...]
// ]
```

## 异常处理

所有方法在失败时会抛出 `CommonException` 异常：

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

try {
    $result = WechatPayByJSAPIFacade::prePayOrder($config, $orderData);
} catch (CommonException $e) {
    $error = $e->getErrorResponse();
    // [
    //     'code' => 51060,
    //     'error' => 'PrePayOrderByWechatJsError',
    //     'msg' => '微信JSAPI下单失败'
    // ]

    echo "错误码: {$error['code']}\n";
    echo "错误信息: {$error['msg']}\n";
}
```

## 错误码说明

| 错误码 | 错误标识 | 说明 |
|--------|----------|------|
| 51000 | WechatMerchantMerchantIdError | 微信商户号未设置 |
| 51010 | WechatMerchantMerchantSerialNumberError | 商户API证书序列号未设置 |
| 51020 | WechatMerchantMerchantPrivateKeyError | 商户私钥文件不存在或无法读取 |
| 51030 | WechatMerchantWechatpayCertificateError | 微信支付平台证书文件不存在或无法读取 |
| 51040 | WechatOfficialAppIdError | 微信公众号AppId未设置 |
| 51050 | WecahtMerchantNotifyUrlJsPayNotifyUrlError | JSAPI支付回调通知地址未设置 |
| 51060 | PrePayOrderByWechatJsError | 微信JSAPI下单失败 |

## 注意事项

1. **证书文件权限**：确保PHP进程有权限读取证书文件，Linux建议设置为644或755权限
2. **证书路径**：证书文件路径必须是绝对路径
3. **订单号唯一性**：`out_trade_no` 必须全局唯一，重复使用会导致下单失败
4. **金额单位**：金额单位为"分"，不是"元"
5. **OpenID**：JSAPI支付需要用户的OpenID，需先通过网页授权获取
6. **回调处理**：支付回调需要验证签名和解密数据，调用支付回调门面

## 示例：完整的支付流程

```php
// 1. 配置
$config = [
    'merchantId' => 'your_merchant_id',
    'merchantSerialNumber' => 'your_serial_number',
    'merchantPrivateKeyPath' => '/path/to/your/apiclient_key.pem',
    'wechatpayCertificatePath' => '/path/to/your/wechatpay.pem',
    'officialAppid' => 'YOUR_WECHAT_OFFICIAL_APPID',
    'notifyUrl' => 'https://yourdomain.com/pay/notify'
];

// 2. 下单
$orderData = [
    'description' => '测试商品',
    'out_trade_no' => date('YmdHis') . rand(1000, 9999),
    'amount' => [
        'total' => 100,
        'currency' => 'CNY'
    ],
    'payer' => [
        'openid' => 'user_openid'
    ]
];

$result = WechatPayByJSAPIFacade::prePayOrder($config, $orderData);

// 3. 将结果返回给前端，前端调起支付
// wx.chooseWXPay({
//     timeStamp: result.timeStamp,
//     nonceStr: result.nonceStr,
//     package: 'prepay_id=' + result.prepay_id,
//     signType: 'RSA',
//     paySign: result.paySign,
//     success: function(res) { }
// });

// 4. 支付成功后，查询订单确认
$orderInfo = WechatPayByJSAPIFacade::queryOrder($config, $orderData['out_trade_no']);
if ($orderInfo['trade_state'] === 'SUCCESS') {
    // 处理支付成功逻辑
}
```

## 官方文档

- [微信支付API文档](https://pay.weixin.qq.com/wiki/doc/apiv3/index.shtml)
- [微信支付SDK](https://github.com/wechatpay-apiv3/wechatpay-guzzle-middleware)

<?php
/*
 * @Descripttion: 微信JSAPI支付服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-07 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-07 00:01:25
 * @FilePath: App.Service.V1.Wechat.Pay.JSAPI.WechatPayByJSAPIFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Wechat\Pay\JSAPI;

use YouHuJun\Tool\App\Exceptions\CommonException;
use GuzzleHttp\Exception\RequestException;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;

/**
 * 微信JSAPI支付服务类
 *
 * 通过方法参数传递微信支付配置,去除Laravel框架耦合
 */
class WechatPayByJSAPIFacadeService
{
    /**
     * 商户号
     * @var string
     */
    private $merchantId;

    /**
     * 商户API证书序列号
     * @var string
     */
    private $merchantSerialNumber;

    /**
     * 商户私钥资源
     * @var resource|string
     */
    private $merchantPrivateKey;

    /**
     * 微信支付平台证书
     * @var mixed
     */
    private $wechatpayCertificate;

    /**
     * Guzzle HTTP Client
     * @var Client
     */
    private $client;

    /**
     * 微信公众号AppId
     * @var string
     */
    private $officialAppid;

    /**
     * 支付回调通知地址
     * @var string
     */
    private $notifyUrl;

    /**
     * 初始化HTTP客户端和支付配置
     *
     * @param array $config 支付配置
     * @return void
     * @throws CommonException
     */
    private function init(array $config): void
    {
        $this->initClient($config);
        $this->otherInit($config);
    }

    /**
     * 初始化HTTP客户端
     *
     * @param array $config 支付配置
     * @return void
     * @throws CommonException
     */
    private function initClient(array $config): void
    {
        $this->merchantId = trim($config['merchantId'] ?? '');
        if (!$this->merchantId) {
            throw new CommonException('WechatMerchantMerchantIdError');
        }

        $this->merchantSerialNumber = trim($config['merchantSerialNumber'] ?? '');
        if (!$this->merchantSerialNumber) {
            throw new CommonException('WechatMerchantMerchantSerialNumberError');
        }

        $merchantPrivateKeyPath = trim($config['merchantPrivateKeyPath'] ?? '');
        if (!$merchantPrivateKeyPath || !file_exists($merchantPrivateKeyPath)) {
            throw new CommonException('WechatMerchantMerchantPrivateKeyError');
        }
        $this->merchantPrivateKey = PemUtil::loadPrivateKey($merchantPrivateKeyPath);

        $wechatpayCertificatePath = trim($config['wechatpayCertificatePath'] ?? '');
        if (!$wechatpayCertificatePath || !file_exists($wechatpayCertificatePath)) {
            throw new CommonException('WechatMerchantWechatpayCertificateError');
        }
        $this->wechatpayCertificate = PemUtil::loadCertificate($wechatpayCertificatePath);

        // 构造一个WechatPayMiddleware
        $wechatpayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($this->merchantId, $this->merchantSerialNumber, $this->merchantPrivateKey)
            ->withWechatPay([$this->wechatpayCertificate])
            ->build();

        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');

        // 创建Guzzle HTTP Client
        $this->client = new Client(['handler' => $stack]);
    }

    /**
     * 其他需要初始化的配置
     *
     * @param array $config 支付配置
     * @return void
     * @throws CommonException
     */
    private function otherInit(array $config): void
    {
        $this->officialAppid = trim($config['officialAppid'] ?? '');
        if (!$this->officialAppid) {
            throw new CommonException('WechatOfficialAppIdError');
        }

        $this->notifyUrl = trim($config['notifyUrl'] ?? '');
        if (!$this->notifyUrl) {
            throw new CommonException('WecahtMerchantNotifyUrlJsPayNotifyUrlError');
        }
    }

    /**
     * JSAPI下单
     *
     * @param array $config 支付配置
     * @param array $orderData 订单数据
     * @return array 返回预支付ID和签名信息
     * @throws CommonException
     */
    public function prePayOrder(array $config, array $orderData): array
    {
        $this->init($config);

        $preJsonData = [
            'appid' => $this->officialAppid,
            'mchid' => $this->merchantId,
            'notify_url' => $this->notifyUrl
        ];

        $requestData = array_merge($preJsonData, $orderData);

        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';

        try {
            $resp = $this->client->request(
                'POST',
                $url,
                [
                    'json' => $requestData,
                    'headers' => ['Accept' => 'application/json']
                ]
            );

            $statusCode = $resp->getStatusCode();

            if ($statusCode == 200) {
                $resultString = $resp->getBody()->getContents();
                $resultObject = json_decode($resultString);

                if (!property_exists($resultObject, 'prepay_id')) {
                    throw new CommonException('PrePayOrderByWechatJsError');
                }

                $prepayId = $resultObject->prepay_id;
                $timeStamp = time();
                $nonceStr = uniqid();

                $content = "{$this->officialAppid}\n{$timeStamp}\n{$nonceStr}\nprepay_id={$prepayId}\n";

                $signature = '';
                openssl_sign($content, $signature, $this->merchantPrivateKey, 'SHA256');
                $paySign = base64_encode($signature);

                return [
                    'prepay_id' => $prepayId,
                    'appId' => $this->officialAppid,
                    'timeStamp' => $timeStamp,
                    'nonceStr' => $nonceStr,
                    'paySign' => $paySign
                ];
            } elseif ($statusCode == 204) {
                // 处理成功，无返回Body
                return [];
            }
        } catch (RequestException $e) {
            // 进行错误处理
            if ($e->hasResponse()) {
                $response = $e->getResponse()->getBody()->getContents();
                // 可以记录日志
            }
            throw new CommonException('PrePayOrderByWechatJsError');
        } catch (\Exception $e) {
            throw new CommonException('PrePayOrderByWechatJsError');
        }
    }

    /**
     * 查询订单
     *
     * @param array $config 支付配置
     * @param string $outTradeNo 商户订单号
     * @return array 订单信息
     * @throws CommonException
     */
    public function queryOrder(array $config, string $outTradeNo): array
    {
        $this->init($config);

        $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/{$outTradeNo}?mchid={$this->merchantId}";

        try {
            $resp = $this->client->request(
                'GET',
                $url,
                ['headers' => ['Accept' => 'application/json']]
            );

            $result = json_decode($resp->getBody()->getContents(), true) ?: [];

            return $result;
        } catch (RequestException $e) {
            throw new CommonException('PrePayOrderByWechatJsError');
        }
    }

    /**
     * 关闭订单
     *
     * @param array $config 支付配置
     * @param string $outTradeNo 商户订单号
     * @return bool
     * @throws CommonException
     */
    public function closeOrder(array $config, string $outTradeNo): bool
    {
        $this->init($config);

        $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/{$outTradeNo}/close";

        try {
            $resp = $this->client->request(
                'POST',
                $url,
                [
                    'json' => ['mchid' => $this->merchantId],
                    'headers' => ['Accept' => 'application/json']
                ]
            );

            return true;
        } catch (RequestException $e) {
            throw new CommonException('PrePayOrderByWechatJsError');
        }
    }

    /**
     * 申请退款
     *
     * @param array $config 支付配置
     * @param array $refundData 退款数据
     * @return array 退款结果
     * @throws CommonException
     */
    public function refund(array $config, array $refundData): array
    {
        $this->init($config);

        $url = 'https://api.mch.weixin.qq.com/v3/refund/domestic/refunds';

        try {
            $resp = $this->client->request(
                'POST',
                $url,
                [
                    'json' => $refundData,
                    'headers' => ['Accept' => 'application/json']
                ]
            );

            $result = json_decode($resp->getBody()->getContents(), true) ?: [];

            return $result;
        } catch (RequestException $e) {
            throw new CommonException('PrePayOrderByWechatJsError');
        }
    }

    /**
     * 查询退款
     *
     * @param array $config 支付配置
     * @param string $outRefundNo 商户退款单号
     * @return array 退款信息
     * @throws CommonException
     */
    public function queryRefund(array $config, string $outRefundNo): array
    {
        $this->init($config);

        $url = "https://api.mch.weixin.qq.com/v3/refund/domestic/refunds/{$outRefundNo}";

        try {
            $resp = $this->client->request(
                'GET',
                $url,
                ['headers' => ['Accept' => 'application/json']]
            );

            $result = json_decode($resp->getBody()->getContents(), true) ?: [];

            return $result;
        } catch (RequestException $e) {
            throw new CommonException('PrePayOrderByWechatJsError');
        }
    }
}

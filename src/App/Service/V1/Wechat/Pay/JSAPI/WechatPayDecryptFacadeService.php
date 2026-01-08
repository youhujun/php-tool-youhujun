<?php
/*
 * @Descripttion: 微信JSAPI支付回调解密服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-07 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-07 00:01:25
 * @FilePath: App.Service.V1.Wechat.Pay.JSAPI.WechatPayDecryptFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Wechat\Pay\JSAPI;

use YouHuJun\Tool\App\Exceptions\CommonException;

use WeChatPay\Crypto\Rsa;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Formatter;

/**
 * 微信JSAPI支付回调解密服务类
 *
 * 验证签名并解密微信支付回调数据
 */
class WechatPayDecryptFacadeService
{
    /**
     * 时间偏移量允许值(秒)
     * @var int
     */
    private $timeOffset = 300;

    /**
     * 验证签名并解密微信支付回调数据
     *
     * @param array $config 支付配置
     * @param array $notifyData 回调数据
     * @return array 解密后的数据
     * @throws CommonException
     */
    public function decryptData(array $config, array $notifyData): array
    {
        // 解构回调数据
        [
            'wechatpay_signature' => $wechatpaySignature,
            'wechatpay_timestamp' => $wechatpayTimestamp,
            'wechatpay_serial' => $wechatpaySerial,
            'wechatpay_nonce' => $wechatpayNonce,
            'body' => $body
        ] = $notifyData;

        // 获取APIv3密钥
        $apiv3Key = trim($config['apiv3Key'] ?? '');
        if (!$apiv3Key) {
            throw new CommonException('WechatApiV3KKeyNotExistsError');
        }

        // 获取微信支付平台证书路径
        $wechatpayCertificatePath = trim($config['wechatpayCertificatePath'] ?? '');
        if (!$wechatpayCertificatePath || !file_exists($wechatpayCertificatePath)) {
            throw new CommonException('WechatMerchantWechatpayCertificateError');
        }

        // 构造平台证书实例
        $wechatpayCertificate = 'file://' . $wechatpayCertificatePath;
        $platformPublicKeyInstance = Rsa::from($wechatpayCertificate, Rsa::KEY_TYPE_PUBLIC);

        // 检查通知时间偏移量，允许5分钟之内的偏移
        $timeOffsetStatus = $this->timeOffset >= abs(Formatter::timestamp() - (int)$wechatpayTimestamp);

        // 验证签名
        $verifiedStatus = Rsa::verify(
            // 构造验签名串
            Formatter::joinedByLineFeed($wechatpayTimestamp, $wechatpayNonce, $body),
            $wechatpaySignature,
            $platformPublicKeyInstance
        );

        if ($timeOffsetStatus && $verifiedStatus) {
            // 转换通知的JSON文本消息为PHP Array数组
            $inBodyArray = (array)json_decode($body, true);

            // 使用PHP7的数据解构语法，从Array中解构并赋值变量
            if (!isset($inBodyArray['resource'])) {
                throw new CommonException('PrePayOrderByWechatJsError');
            }

            ['resource' => [
                'ciphertext' => $ciphertext,
                'nonce' => $nonce,
                'associated_data' => $aad
            ]] = $inBodyArray;

            // 加密文本消息解密
            $inBodyResource = AesGcm::decrypt($ciphertext, $apiv3Key, $nonce, $aad);

            // 把解密后的文本转换为PHP Array数组
            $inBodyResourceArray = (array)json_decode($inBodyResource, true);

            return $inBodyResourceArray;
        }

        throw new CommonException('PrePayOrderByWechatJsError');
    }

    /**
     * 设置时间偏移量(秒)
     *
     * @param int $seconds 时间偏移量
     * @return void
     */
    public function setTimeOffset(int $seconds): void
    {
        $this->timeOffset = $seconds;
    }

    /**
     * 获取时间偏移量(秒)
     *
     * @return int 时间偏移量
     */
    public function getTimeOffset(): int
    {
        return $this->timeOffset;
    }
}

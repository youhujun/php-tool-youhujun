<?php
/*
 * @Descripttion: 腾讯云短信服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.SMS.Tencent.TencentSMSFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\SMS\Tencent;

use YouHuJun\Tool\App\Exceptions\CommonException;

// 导入腾讯云短信SDK
use TencentCloud\Sms\V20210111\SmsClient;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

/**
 * 腾讯云短信服务类
 *
 * 提供腾讯云短信相关的功能,通过方法参数传递配置,去除框架耦合
 */
class TencentSMSFacadeService
{
    /**
     * 发送短信验证码
     *
     * @param array $config 腾讯云配置,包含 secretId, secretKey 等参数
     * @param array $smsParam 短信参数,包含 smsContent, phoneNumber, smsSdkAppId, signName, templateId 等
     * @return int 返回发送结果: 1-成功, 0-失败
     * @throws CommonException
     */
    public function sendSms(array $config, array $smsParam): int
    {
        // 定义发送结果
        $sendResult = 0;

        // 验证必传的腾讯云凭证
        $secretId = trim($config['secretId'] ?? '');
        if (!$secretId) {
            throw new CommonException('TencentCloudSecretIdError');
        }

        $secretKey = trim($config['secretKey'] ?? '');
        if (!$secretKey) {
            throw new CommonException('TencentCloudSecretKeyError');
        }

        // 提取短信参数
        ['smsContent' => $smsContent, 'phoneNumber' => $phoneNumber] = $smsParam;

        // 验证短信内容
        if (!isset($smsParam['smsContent']) || count($smsParam['smsContent']) == 0) {
            throw new CommonException('TencentCloudSmsContentError');
        }

        // 验证手机号
        if (!isset($smsParam['phoneNumber']) || count($smsParam['phoneNumber']) == 0) {
            throw new CommonException('TencentCloudSmsPhoneNumberError');
        }

        // 获取可选参数,如果未提供则从config中获取
        $smsApConfig = $smsParam['smsApConfig'] ?? ($config['smsApConfig'] ?? '');
        $smsSdkAppId = $smsParam['smsSdkAppId'] ?? ($config['smsSdkAppId'] ?? '');
        $signName = $smsParam['signName'] ?? ($config['signName'] ?? '');
        $templateId = $smsParam['templateId'] ?? ($config['templateId'] ?? '');
        $phonePre = $smsParam['phonePre'] ?? ($config['phonePre'] ?? '');
        $curlMethods = $smsParam['curlMethods'] ?? 'POST';
        $signMethods = $smsParam['signMethods'] ?? 'TC3-HMAC-SHA256';

        // 验证必传参数
        if (!$smsApConfig) {
            throw new CommonException('TencentCloudSmsApConfigError');
        }

        if (!$smsSdkAppId) {
            throw new CommonException('TencentCloudSmsSdkAppIdError');
        }

        if (!$signName) {
            throw new CommonException('TencentCloudSmsSignNameError');
        }

        if (!$templateId) {
            throw new CommonException('TencentCloudSmsTemplateIdError');
        }

        if (!$phonePre) {
            throw new CommonException('TencentCloudSmsPhonePreError');
        }

        try {
            // 实例化证书对象
            $cred = new Credential($secretId, $secretKey);

            // 实例化HTTP选项
            $httpProfile = new HttpProfile();
            $httpProfile->setReqMethod($curlMethods);  // 请求方法
            $httpProfile->setReqTimeout(10);           // 请求超时时间,单位为秒
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");  // 指定接入地域域名

            // 实例化client选项
            $clientProfile = new ClientProfile();
            $clientProfile->setSignMethod($signMethods);  // 指定签名算法
            $clientProfile->setHttpProfile($httpProfile);

            // 实例化要请求产品的client对象
            $client = new SmsClient($cred, $smsApConfig, $clientProfile);

            // 实例化短信发送请求对象
            $req = new SendSmsRequest();

            /* 填充请求参数 */
            // 短信应用ID
            $req->SmsSdkAppId = $smsSdkAppId;

            // 短信签名内容
            $req->SignName = $signName;

            // 模板ID
            $req->TemplateId = $templateId;

            // 模板参数
            $req->TemplateParamSet = $smsContent;

            // 下发手机号码,添加手机号前缀
            foreach ($phoneNumber as $key => &$value) {
                $value = $phonePre . $value;
            }
            $req->PhoneNumberSet = $phoneNumber;

            // 用户的session内容(无需要可忽略)
            $req->SessionContext = "";

            // 短信码号扩展号(无需要可忽略)
            $req->ExtendCode = "";

            // 国际/港澳台短信SenderId(无需要可忽略)
            $req->SenderId = "";

            // 通过client对象调用SendSms方法发起请求
            $resp = $client->SendSms($req);

            // 检查返回结果
            if (is_object($resp)) {
                if ($resp->SendStatusSet[0]->Code === "Ok") {
                    $sendResult = 1;
                }
            } else {
                throw new CommonException('TencentCloudSmsSendError');
            }

            return $sendResult;
        } catch (TencentCloudSDKException $e) {
            throw new CommonException('TencentCloudSmsError');
        }
    }
}

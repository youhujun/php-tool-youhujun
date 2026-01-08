<?php
/*
 * @Descripttion: 微信小程序服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.Wechat.MiniProgram.WechatMiniProgramFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Wechat\MiniProgram;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * 微信小程序服务类
 *
 * 提供微信小程序相关的功能,通过方法参数传递配置,去除框架耦合
 */
class WechatMiniProgramFacadeService
{
    /**
     * 通过code获取微信小程序用户的openid和session_key
     *
     * @param array $params 参数,包含 code 和 appid
     * @param array $config 配置,包含 appsecret
     * @return array 返回包含openid、session_key等信息的数组
     * @throws CommonException
     */
    public function getOpenIdByCode(array $params, array $config): array
    {
        // 验证必填参数
        if (!(isset($params['code']) || isset($params['anonymousCode']))) {
            throw new CommonException('ParamsIsNullError');
        }

        if (!isset($params['appid'])) {
            throw new CommonException('AppidIsNullError');
        }

        ['code' => $code, 'appid' => $appid] = $params;

        // 从配置中获取appsecret
        $secret = $config['appsecret'] ?? '';
        if (!$secret) {
            throw new CommonException('WechatMiniProgramSecretRequired');
        }

        // 构造请求URL
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";

        // 发送HTTP GET请求
        $response = httpGet($url);

        // 解析响应
        $result = json_decode($response, true);

        // 检查是否返回错误
        if (isset($result['errcode']) && $result['errcode'] !== 0) {
            throw new CommonException('WechatMiniProgramLoginError');
        }

        // 返回结果数据
        return [
            'response' => $result,
            'appid' => $appid
        ];
    }

    /**
     * 通过code获取微信小程序用户的openid和session_key(返回集合对象格式)
     *
     * @param array $params 参数,包含 code 和 appid
     * @param array $config 配置,包含 appsecret
     * @return object 返回集合对象
     * @throws CommonException
     */
    public function getOpenIdByCodeWithCollection(array $params, array $config): object
    {
        $result = $this->getOpenIdByCode($params, $config);

        return (object)$result;
    }
}

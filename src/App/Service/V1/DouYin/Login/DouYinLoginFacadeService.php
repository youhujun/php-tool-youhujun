<?php
/*
 * @Descripttion: 抖音登录服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.DouYin.Login.DouYinLoginFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\DouYin\Login;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * 抖音登录服务类
 *
 * 提供抖音小程序和抖音小游戏登录相关的功能,通过方法参数传递配置,去除框架耦合
 */
class DouYinLoginFacadeService
{
    /**
     * 抖音小游戏登录 - 通过code获取openid和session_key
     *
     * @param array $params 参数,包含 code/anonymousCode 和 appid
     * @param array $config 配置,包含 appsecret
     * @return array 返回包含openid、session_key等信息的数组
     * @throws CommonException
     */
    public function getOpenIdByCodeWithMiniGame(array $params, array $config): array
    {
        // 验证必填参数
        if (!(isset($params['code']) || isset($params['anonymousCode']))) {
            throw new CommonException('ParamsIsNullError');
        }

        if (!isset($params['appid'])) {
            throw new CommonException('AppidIsNullError');
        }

        ['code' => $code, 'anonymousCode' => $anonymousCode, 'appid' => $appid] = $params;

        // 从配置中获取appsecret
        $secret = $config['appsecret'] ?? '';
        if (!$secret) {
            throw new CommonException('DouYinMiniGameSecretRequired');
        }

        // 构造请求URL
        $url = "https://minigame.zijieapi.com/mgplatform/api/apps/jscode2session?appid={$appid}&secret={$secret}&code={$code}&anonymousCode={$anonymousCode}";

        // 发送HTTP GET请求
        $response = httpGet($url);

        // 解析响应
        $result = json_decode($response, true);

        // 检查是否返回错误
        if (isset($result['error']) && $result['error'] !== 0) {
            throw new CommonException('DouYinMiniGameLoginError');
        }

        // 返回结果数据
        return [
            'response' => $result,
            'appid' => $appid
        ];
    }

    /**
     * 抖音小程序登录 - 通过code获取openid和session_key
     *
     * @param array $params 参数,包含 code/anonymousCode 和 appid
     * @param array $config 配置,包含 appsecret
     * @return array 返回包含openid、session_key等信息的数组
     * @throws CommonException
     */
    public function getOpenIdByCodeWithMiniProgram(array $params, array $config): array
    {
        // 验证必填参数
        if (!(isset($params['code']) || isset($params['anonymousCode']))) {
            throw new CommonException('ParamsIsNullError');
        }

        if (!isset($params['appid'])) {
            throw new CommonException('AppidIsNullError');
        }

        ['code' => $code, 'anonymousCode' => $anonymousCode, 'appid' => $appid] = $params;

        // 从配置中获取appsecret
        $secret = $config['appsecret'] ?? '';
        if (!$secret) {
            throw new CommonException('DouYinMiniProgramSecretRequired');
        }

        // 构造请求URL
        $url = "https://developer.toutiao.com/api/apps/v2/jscode2session";

        // 请求头
        $headers = ['Content-Type:application/json', 'charset=utf-8'];

        // 请求参数
        $data = [
            'appid' => $appid,
            'secret' => $secret,
            'code' => $code,
            'anonymousCode' => $anonymousCode
        ];

        // 发送HTTP POST请求
        $response = httpPost($url, $headers, json_encode($data));

        // 解析响应
        $result = json_decode($response, true);

        // 检查是否返回错误
        if (isset($result['err_no']) && $result['err_no'] !== 0) {
            throw new CommonException('DouYinMiniProgramLoginError');
        }

        // 返回结果数据
        return [
            'response' => $result,
            'appid' => $appid
        ];
    }
}

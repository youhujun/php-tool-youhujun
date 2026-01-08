<?php
/*
 * @Descripttion: 抖音登录服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Facade.V1.DouYin.Login.DouYinLoginFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facade\V1\DouYin\Login;

use YouHuJun\Tool\App\Service\V1\DouYin\Login\DouYinLoginFacadeService;

/**
 * 抖音登录服务静态门面类
 *
 * 提供静态方法调用抖音登录服务
 */
class DouYinLoginFacade
{
    /**
     * 服务实例(单例)
     *
     * @var DouYinLoginFacadeService|null
     */
    private static ?DouYinLoginFacadeService $instance = null;

    /**
     * 获取服务实例
     *
     * @return DouYinLoginFacadeService
     */
    private static function getInstance(): DouYinLoginFacadeService
    {
        if (self::$instance === null) {
            self::$instance = new DouYinLoginFacadeService();
        }

        return self::$instance;
    }

    /**
     * 抖音小游戏登录 - 通过code获取openid和session_key
     *
     * @param array $params 参数,包含 code/anonymousCode 和 appid
     * @param array $config 配置,包含 appsecret
     * @return array 返回包含openid、session_key等信息的数组
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getOpenIdByCodeWithMiniGame(array $params, array $config): array
    {
        return self::getInstance()->getOpenIdByCodeWithMiniGame($params, $config);
    }

    /**
     * 抖音小程序登录 - 通过code获取openid和session_key
     *
     * @param array $params 参数,包含 code/anonymousCode 和 appid
     * @param array $config 配置,包含 appsecret
     * @return array 返回包含openid、session_key等信息的数组
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getOpenIdByCodeWithMiniProgram(array $params, array $config): array
    {
        return self::getInstance()->getOpenIdByCodeWithMiniProgram($params, $config);
    }
}

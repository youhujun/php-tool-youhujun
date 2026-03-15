<?php
/*
 * @Descripttion: 认证签名服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-17 02:49:09
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-17 15:36:45
 * @FilePath: \php-tool-youhujun\src\App\Facades\V1\Utils\Sign\AuthSignFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facades\V1\Utils\Sign;

use YouHuJun\Tool\App\Services\V1\Utils\Sign\AuthSignFacadeService;
use BadMethodCallException;

/**
 * 认证签名服务静态门面类
 *
 * 提供静态方法调用认证签名服务
 */
class AuthSignFacade
{
    /**
     * 服务实例(单例)
     *
     * @var AuthSignFacadeService|null
     */
    private static ?AuthSignFacadeService $instance = null;

    /**
     * 获取服务实例
     *
     * @return AuthSignFacadeService
     */
    private static function getInstance(): AuthSignFacadeService
    {
        if (self::$instance === null) {
            self::$instance = new AuthSignFacadeService();
        }

        return self::$instance;
    }

    /**
     * 生成HMAC-SHA256签名
     *
     * @param array $params 待签名的参数数组
     * @param string $secretKey 密钥
     * @return string 生成的签名
     */
    public static function makeSign($params, $secretKey): string
    {
        return self::getInstance()->makeSign($params, $secretKey);
    }

    /**
     * 动态调用未在Facade中显式声明的方法
     *
     * @param string $method 方法名
     * @param array $parameters 参数数组
     * @return mixed
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $instance = static::getInstance();
        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($instance), $method)
            );
        }
        return $instance->$method(...$parameters);
    }
}

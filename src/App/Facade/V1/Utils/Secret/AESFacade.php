<?php
/*
 * @Descripttion: AES加解密服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Facade.V1.Utils.Secret.AESFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facade\V1\Utils\Secret;

use YouHuJun\Tool\App\Service\V1\Utils\Secret\AESFacadeService;

/**
 * AES加解密服务静态门面类
 *
 * 提供静态方法调用AES加解密服务
 */
class AESFacade
{
    /**
     * 服务实例(单例)
     *
     * @var AESFacadeService|null
     */
    private static ?AESFacadeService $instance = null;

    /**
     * 获取服务实例
     *
     * @return AESFacadeService
     */
    private static function getInstance(): AESFacadeService
    {
        if (self::$instance === null) {
            self::$instance = new AESFacadeService();
        }

        return self::$instance;
    }

    /**
     * 使用AES算法加密字符串
     *
     * @param string $data 待加密的字符串
     * @param string $key 加密密钥
     * @param string|null $method 可选,加密方法,不传则使用默认 AES-256-CBC
     * @param string|null $iv 可选,初始化向量,不传则使用默认 '0123456789ABEDEF'
     * @return string 返回Base64编码后的加密结果
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function encrypt(string $data, string $key, ?string $method = null, ?string $iv = null): string
    {
        return self::getInstance()->encrypt($data, $key, $method, $iv);
    }

    /**
     * 使用AES算法解密字符串
     *
     * @param string $data 需要解密的Base64编码字符串
     * @param string $key 解密密钥
     * @param string|null $method 可选,加密方法,不传则使用默认 AES-256-CBC
     * @param string|null $iv 可选,初始化向量,不传则使用默认 '0123456789ABEDEF'
     * @return string 返回解密后的原始字符串
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function decrypt(string $data, string $key, ?string $method = null, ?string $iv = null): string
    {
        return self::getInstance()->decrypt($data, $key, $method, $iv);
    }
}

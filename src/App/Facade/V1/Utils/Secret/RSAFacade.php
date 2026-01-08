<?php
/*
 * @Descripttion: RSA加解密服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Facade.V1.Utils.Secret.RSAFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facade\V1\Utils\Secret;

use YouHuJun\Tool\App\Service\V1\Utils\Secret\RSAFacadeService;

/**
 * RSA加解密服务静态门面类
 *
 * 提供静态方法调用RSA加解密服务
 */
class RSAFacade
{
    /**
     * 服务实例(单例)
     *
     * @var RSAFacadeService|null
     */
    private static ?RSAFacadeService $instance = null;

    /**
     * 获取服务实例
     *
     * @return RSAFacadeService
     */
    private static function getInstance(): RSAFacadeService
    {
        if (self::$instance === null) {
            self::$instance = new RSAFacadeService();
        }

        return self::$instance;
    }

    /**
     * 使用公钥加密
     *
     * @param string $data 待加密的明文字符串
     * @param string $publicKeyString 公钥字符串
     * @param string $format 公钥格式: pem(默认), der, base64
     * @param int $padding 填充方式,默认 OPENSSL_PKCS1_PADDING
     * @return string Base64编码的加密结果
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function encrypt(string $data, string $publicKeyString, string $format = 'base64', int $padding = OPENSSL_PKCS1_PADDING): string
    {
        return self::getInstance()->encrypt($data, $publicKeyString, $format, $padding);
    }

    /**
     * 使用私钥解密
     *
     * @param string $data Base64编码的加密字符串
     * @param string $privateKeyString 私钥字符串
     * @param string $format 私钥格式: pem(默认), der, base64
     * @param string|null $passphrase 私钥密码,如果有
     * @param int $padding 填充方式,默认 OPENSSL_PKCS1_PADDING
     * @return string 解密后的原始字符串
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function decrypt(string $data, string $privateKeyString, string $format = 'base64', ?string $passphrase = null, int $padding = OPENSSL_PKCS1_PADDING): string
    {
        return self::getInstance()->decrypt($data, $privateKeyString, $format, $passphrase, $padding);
    }
}

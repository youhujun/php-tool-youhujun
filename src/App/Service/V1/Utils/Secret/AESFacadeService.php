<?php
/*
 * @Descripttion: AES加解密服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.Utils.Secret.AESFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Utils\Secret;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * AES加解密服务类
 *
 * 提供AES加密和解密功能,支持自定义加密方法和IV
 */
class AESFacadeService
{
    /**
     * 加密方法,默认 AES-256-CBC
     *
     * @var string
     */
    private string $method = 'AES-256-CBC';

    /**
     * 初始化向量IV,默认固定值
     *
     * @var string
     */
    private string $iv = '0123456789ABEDEF';

    /**
     * 构造函数,可初始化加密方法和IV
     *
     * @param string|null $method 加密方法,默认 AES-256-CBC
     * @param string|null $iv 初始化向量,默认 '0123456789ABEDEF'
     */
    public function __construct(?string $method = null, ?string $iv = null)
    {
        if ($method !== null) {
            $this->setMethod($method);
        }

        if ($iv !== null) {
            $this->setIV($iv);
        }
    }

    /**
     * 设置加密方法
     *
     * @param string $method 加密方法,如 AES-128-CBC, AES-256-CBC 等
     * @return void
     * @throws CommonException
     */
    public function setMethod(string $method): void
    {
        $validMethods = [
            'AES-128-CBC',
            'AES-192-CBC',
            'AES-256-CBC',
            'AES-128-ECB',
            'AES-192-ECB',
            'AES-256-ECB'
        ];

        if (!in_array($method, $validMethods)) {
            throw new CommonException('AESMethodError');
        }

        $this->method = $method;
    }

    /**
     * 设置初始化向量IV
     *
     * @param string $iv 初始化向量,长度需与加密方法匹配
     * @return void
     * @throws CommonException
     */
    public function setIV(string $iv): void
    {
        $this->iv = $iv;
    }

    /**
     * 使用AES算法加密字符串
     *
     * @param string $data 待加密的字符串
     * @param string $key 加密密钥
     * @param string|null $method 可选,加密方法,不传则使用当前设置的方法
     * @param string|null $iv 可选,初始化向量,不传则使用当前设置的IV
     * @return string 返回Base64编码后的加密结果
     * @throws CommonException
     */
    public function encrypt(string $data, string $key, ?string $method = null, ?string $iv = null): string
    {
        $encryptMethod = $method ?? $this->method;
        $encryptIV = $iv ?? $this->iv;

        if (empty($data)) {
            throw new CommonException('AESEncryptDataEmpty');
        }

        if (empty($key)) {
            throw new CommonException('AESEncryptKeyEmpty');
        }

        $encryptedText = openssl_encrypt(
            $data,
            $encryptMethod,
            $key,
            OPENSSL_RAW_DATA,
            $encryptIV
        );

        if ($encryptedText === false) {
            throw new CommonException('AESEncryptFailed');
        }

        return base64_encode($encryptedText);
    }

    /**
     * 使用AES算法解密字符串
     *
     * @param string $data 需要解密的Base64编码字符串
     * @param string $key 解密密钥
     * @param string|null $method 可选,加密方法,不传则使用当前设置的方法
     * @param string|null $iv 可选,初始化向量,不传则使用当前设置的IV
     * @return string 返回解密后的原始字符串
     * @throws CommonException
     */
    public function decrypt(string $data, string $key, ?string $method = null, ?string $iv = null): string
    {
        $decryptMethod = $method ?? $this->method;
        $decryptIV = $iv ?? $this->iv;

        if (empty($data)) {
            throw new CommonException('AESDecryptDataEmpty');
        }

        if (empty($key)) {
            throw new CommonException('AESDecryptKeyEmpty');
        }

        $encrypted = base64_decode($data);

        if ($encrypted === false) {
            throw new CommonException('AESDecryptDataInvalid');
        }

        $decrypted = openssl_decrypt(
            $encrypted,
            $decryptMethod,
            $key,
            OPENSSL_RAW_DATA,
            $decryptIV
        );

        if ($decrypted === false) {
            throw new CommonException('AESDecryptFailed');
        }

        return $decrypted;
    }

    /**
     * 获取当前加密方法
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * 获取当前IV
     *
     * @return string
     */
    public function getIV(): string
    {
        return $this->iv;
    }
}

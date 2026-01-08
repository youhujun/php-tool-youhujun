<?php
/*
 * @Descripttion: RSA加解密服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.Utils.Secret.RSAFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Utils\Secret;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * RSA加解密服务类
 *
 * 提供RSA公钥加密和私钥解密功能
 */
class RSAFacadeService
{
    /**
     * 从Base64编码字符串获取公钥资源
     *
     * @param string $publicKeyString Base64编码的公钥
     * @param string $format 公钥格式: pem(默认), der, base64
     * @return resource|false 返回公钥资源,失败返回false
     * @throws CommonException
     */
    public function getPublicKey(string $publicKeyString, string $format = 'base64')
    {
        $publicKeyPem = '';

        // 根据不同格式处理公钥
        switch ($format) {
            case 'pem':
                // 已经是PEM格式,直接使用
                $publicKeyPem = $publicKeyString;
                break;

            case 'der':
                // DER格式转换为PEM格式
                $publicKeyPem = "-----BEGIN PUBLIC KEY-----\n" .
                    chunk_split(base64_encode($publicKeyString), 64, "\n") .
                    "-----END PUBLIC KEY-----";
                break;

            case 'base64':
                // Base64解码后得到DER格式,再转换为PEM格式
                $publicKeyDer = base64_decode($publicKeyString);
                if ($publicKeyDer === false) {
                    throw new CommonException('RSAPublicKeyBase64DecodeFailed');
                }
                $publicKeyPem = "-----BEGIN PUBLIC KEY-----\n" .
                    chunk_split(base64_encode($publicKeyDer), 64, "\n") .
                    "-----END PUBLIC KEY-----";
                break;

            default:
                throw new CommonException('RSAPublicKeyFormatNotSupported');
        }

        // 创建公钥资源
        $publicKey = openssl_pkey_get_public($publicKeyPem);

        if ($publicKey === false) {
            throw new CommonException('RSAPublicKeyLoadFailed');
        }

        return $publicKey;
    }

    /**
     * 从Base64编码字符串获取私钥资源
     *
     * @param string $privateKeyString Base64编码的私钥
     * @param string $format 私钥格式: pem(默认), der, base64
     * @param string|null $passphrase 私钥密码,如果有
     * @return resource|false 返回私钥资源,失败返回false
     * @throws CommonException
     */
    public function getPrivateKey(string $privateKeyString, string $format = 'base64', ?string $passphrase = null)
    {
        $privateKeyPem = '';

        // 根据不同格式处理私钥
        switch ($format) {
            case 'pem':
                // 已经是PEM格式,直接使用
                $privateKeyPem = $privateKeyString;
                break;

            case 'der':
                // DER格式转换为PEM格式
                $privateKeyPem = "-----BEGIN PRIVATE KEY-----\n" .
                    chunk_split(base64_encode($privateKeyString), 64, "\n") .
                    "-----END PRIVATE KEY-----";
                break;

            case 'base64':
                // Base64解码后得到DER格式,再转换为PEM格式
                $privateKeyDer = base64_decode($privateKeyString);
                if ($privateKeyDer === false) {
                    throw new CommonException('RSAPrivateKeyBase64DecodeFailed');
                }
                $privateKeyPem = "-----BEGIN PRIVATE KEY-----\n" .
                    chunk_split(base64_encode($privateKeyDer), 64, "\n") .
                    "-----END PRIVATE KEY-----";
                break;

            default:
                throw new CommonException('RSAPrivateKeyFormatNotSupported');
        }

        // 创建私钥资源
        $privateKey = openssl_pkey_get_private($privateKeyPem, $passphrase);

        if ($privateKey === false) {
            throw new CommonException('RSAPrivateKeyLoadFailed');
        }

        return $privateKey;
    }

    /**
     * 使用公钥加密
     *
     * @param string $data 待加密的明文字符串
     * @param string $publicKeyString 公钥字符串
     * @param string $format 公钥格式: pem(默认), der, base64
     * @param int $padding 填充方式,默认 OPENSSL_PKCS1_PADDING
     * @return string Base64编码的加密结果
     * @throws CommonException
     */
    public function encrypt(string $data, string $publicKeyString, string $format = 'base64', int $padding = OPENSSL_PKCS1_PADDING): string
    {
        if (empty($data)) {
            throw new CommonException('RSAEncryptDataEmpty');
        }

        if (empty($publicKeyString)) {
            throw new CommonException('RSAPublicKeyEmpty');
        }

        try {
            // 获取公钥资源
            $publicKey = $this->getPublicKey($publicKeyString, $format);

            // 使用公钥加密数据
            $success = openssl_public_encrypt($data, $encryptedText, $publicKey, $padding);

            if (!$success) {
                $error = openssl_error_string();
                openssl_free_key($publicKey);
                throw new CommonException('RSAEncryptFailed');
            }

            // 释放公钥资源
            openssl_free_key($publicKey);

            // 将加密结果进行base64编码
            return base64_encode($encryptedText);

        } catch (CommonException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CommonException('RSAEncryptException');
        }
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
     * @throws CommonException
     */
    public function decrypt(string $data, string $privateKeyString, string $format = 'base64', ?string $passphrase = null, int $padding = OPENSSL_PKCS1_PADDING): string
    {
        if (empty($data)) {
            throw new CommonException('RSADecryptDataEmpty');
        }

        if (empty($privateKeyString)) {
            throw new CommonException('RSAPrivateKeyEmpty');
        }

        try {
            // Base64解码加密数据
            $encrypted = base64_decode($data);
            if ($encrypted === false) {
                throw new CommonException('RSADecryptDataInvalid');
            }

            // 获取私钥资源
            $privateKey = $this->getPrivateKey($privateKeyString, $format, $passphrase);

            // 使用私钥解密数据
            $success = openssl_private_decrypt($encrypted, $decryptedText, $privateKey, $padding);

            if (!$success) {
                $error = openssl_error_string();
                openssl_free_key($privateKey);
                throw new CommonException('RSADecryptFailed');
            }

            // 释放私钥资源
            openssl_free_key($privateKey);

            return $decryptedText;

        } catch (CommonException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CommonException('RSADecryptException');
        }
    }
}

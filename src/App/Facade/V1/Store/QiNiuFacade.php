<?php
/*
 * @Descripttion: 七牛云存储服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Facade.V1.Store.QiNiuFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facade\V1\Store;

use YouHuJun\Tool\App\Service\V1\Store\QiNiuFacadeService;

/**
 * 七牛云存储服务静态门面类
 *
 * 提供静态方法调用七牛云存储服务
 * 注意: 此服务依赖七牛云官方SDK (qiniu/php-sdk)
 */
class QiNiuFacade
{
    /**
     * 服务实例(单例)
     *
     * @var QiNiuFacadeService|null
     */
    private static ?QiNiuFacadeService $instance = null;

    /**
     * 初始化配置
     *
     * @param string $accessKey 七牛云AccessKey
     * @param string $secretKey 七牛云SecretKey
     * @param string|null $cdnUrl CDN域名,可选
     * @param int $expires 上传凭证有效期(秒)
     * @return void
     */
    public static function init(string $accessKey, string $secretKey, ?string $cdnUrl = null, int $expires = 7200): void
    {
        self::$instance = new QiNiuFacadeService($accessKey, $secretKey, $cdnUrl, $expires);
    }

    /**
     * 获取服务实例
     *
     * @return QiNiuFacadeService
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    private static function getInstance(): QiNiuFacadeService
    {
        if (self::$instance === null) {
            throw new \YouHuJun\Tool\App\Exceptions\CommonException('QiNiuNotInitialized');
        }

        return self::$instance;
    }

    /**
     * 设置CDN域名
     *
     * @param string $cdnUrl
     * @return void
     */
    public static function setCdnUrl(string $cdnUrl): void
    {
        self::getInstance()->setCdnUrl($cdnUrl);
    }

    /**
     * 设置上传凭证有效期
     *
     * @param int $seconds 有效期(秒)
     * @return void
     */
    public static function setExpires(int $seconds): void
    {
        self::getInstance()->setExpires($seconds);
    }

    /**
     * 设置自定义返回内容
     *
     * @param string $returnBody JSON格式的返回内容
     * @return void
     */
    public static function setReturnBody(string $returnBody): void
    {
        self::getInstance()->setReturnBody($returnBody);
    }

    /**
     * 上传文件到七牛云
     *
     * @param string $filePath 本地文件路径
     * @param string $bucket 存储空间名称
     * @param string $savePath 保存路径(文件key)
     * @param string|null $mimeType MIME类型,默认 application/octet-stream
     * @param bool $checkCrc 是否校验CRC32,默认true
     * @return array 返回上传结果
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function uploadFile(string $filePath, string $bucket, string $savePath, ?string $mimeType = null, bool $checkCrc = true): array
    {
        return self::getInstance()->uploadFile($filePath, $bucket, $savePath, $mimeType, $checkCrc);
    }

    /**
     * 上传文件内容(二进制数据)
     *
     * @param string $data 文件内容
     * @param string $bucket 存储空间名称
     * @param string $savePath 保存路径(文件key)
     * @param string|null $mimeType MIME类型
     * @return array 返回上传结果
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function uploadData(string $data, string $bucket, string $savePath, ?string $mimeType = null): array
    {
        return self::getInstance()->uploadData($data, $bucket, $savePath, $mimeType);
    }

    /**
     * 获取私有空间下载链接
     *
     * @param string $savePath 保存路径(文件key)
     * @param int|null $expires 有效期(秒),不传则使用默认值3600秒
     * @return string 签名后的下载链接
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getPrivateFileUrl(string $savePath, ?int $expires = null): string
    {
        return self::getInstance()->getPrivateFileUrl($savePath, $expires);
    }

    /**
     * 获取公有空间下载链接
     *
     * @param string $savePath 保存路径(文件key)
     * @return string 下载链接
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getPublicFileUrl(string $savePath): string
    {
        return self::getInstance()->getPublicFileUrl($savePath);
    }

    /**
     * 删除文件
     *
     * @param string $bucket 存储空间名称
     * @param string $savePath 文件key
     * @return bool 删除成功返回true
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function deleteFile(string $bucket, string $savePath): bool
    {
        return self::getInstance()->deleteFile($bucket, $savePath);
    }

    /**
     * 获取文件信息
     *
     * @param string $bucket 存储空间名称
     * @param string $savePath 文件key
     * @return array 文件信息
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getFileInfo(string $bucket, string $savePath): array
    {
        return self::getInstance()->getFileInfo($bucket, $savePath);
    }

    /**
     * 获取上传凭证(供客户端使用)
     *
     * @param string $bucket 存储空间名称
     * @param string|null $keyToOverwrite 覆盖上传的文件名
     * @return string 上传凭证
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getUploadToken(string $bucket, ?string $keyToOverwrite = null): string
    {
        return self::getInstance()->getUploadTokenForClient($bucket, $keyToOverwrite);
    }
}

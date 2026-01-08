<?php
/*
 * @Descripttion: 七牛云存储服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.Store.QiNiuFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Store;

use YouHuJun\Tool\App\Exceptions\CommonException;

// 引入七牛云SDK
use Qiniu\Auth as QiniuAuth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

/**
 * 七牛云存储服务类
 *
 * 提供七牛云文件上传和下载链接生成功能
 * 注意: 此服务依赖七牛云官方SDK (qiniu/php-sdk)
 */
class QiNiuFacadeService
{
    /**
     * 七牛云AccessKey
     *
     * @var string
     */
    private string $accessKey;

    /**
     * 七牛云SecretKey
     *
     * @var string
     */
    private string $secretKey;

    /**
     * 存储空间名称
     *
     * @var string
     */
    private string $bucket;

    /**
     * 七牛云认证对象
     *
     * @var QiniuAuth
     */
    private $auth;

    /**
     * 上传凭证
     *
     * @var string
     */
    private string $uploadToken;

    /**
     * CDN域名
     *
     * @var string
     */
    private string $cdnUrl;

    /**
     * 上传凭证有效期(秒),默认2小时
     *
     * @var int
     */
    private int $expires = 7200;

    /**
     * 自定义返回内容
     *
     * @var string|null
     */
    private ?string $returnBody = null;

    /**
     * 构造函数
     *
     * @param string $accessKey 七牛云AccessKey
     * @param string $secretKey 七牛云SecretKey
     * @param string|null $cdnUrl CDN域名,可选
     * @param int $expires 上传凭证有效期(秒)
     * @throws CommonException
     */
    public function __construct(string $accessKey, string $secretKey, ?string $cdnUrl = null, int $expires = 7200)
    {
        if (empty($accessKey)) {
            throw new CommonException('QiNiuAccessKeyError');
        }

        if (empty($secretKey)) {
            throw new CommonException('QiNiuSecretKeyError');
        }

        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->cdnUrl = $cdnUrl ?? '';
        $this->expires = $expires;

        // 创建认证对象
        $this->auth = new QiniuAuth($accessKey, $secretKey);

        // 设置默认返回内容
        $this->returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(fname)"}';
    }

    /**
     * 设置CDN域名
     *
     * @param string $cdnUrl
     * @return void
     */
    public function setCdnUrl(string $cdnUrl): void
    {
        $this->cdnUrl = $cdnUrl;
    }

    /**
     * 设置上传凭证有效期
     *
     * @param int $seconds 有效期(秒)
     * @return void
     */
    public function setExpires(int $seconds): void
    {
        $this->expires = $seconds;
    }

    /**
     * 设置自定义返回内容
     *
     * @param string $returnBody JSON格式的返回内容
     * @return void
     */
    public function setReturnBody(string $returnBody): void
    {
        $this->returnBody = $returnBody;
    }

    /**
     * 获取上传凭证
     *
     * @param string $bucket 存储空间名称
     * @param string|null $keyToOverwrite 覆盖上传的文件名,不传则不覆盖
     * @return string 上传凭证
     * @throws CommonException
     */
    protected function getUploadToken(string $bucket, ?string $keyToOverwrite = null): string
    {
        if (empty($bucket)) {
            throw new CommonException('QiNiuBucketError');
        }

        $this->bucket = $bucket;

        // 构建上传策略
        $policy = [
            'returnBody' => $this->returnBody
        ];

        // 生成上传凭证
        $this->uploadToken = $this->auth->uploadToken(
            $bucket,
            $keyToOverwrite,
            $this->expires,
            $policy,
            true
        );

        return $this->uploadToken;
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
     * @throws CommonException
     */
    public function uploadFile(string $filePath, string $bucket, string $savePath, ?string $mimeType = null, bool $checkCrc = true): array
    {
        if (!file_exists($filePath)) {
            throw new CommonException('QiNiuFileNotFoundError');
        }

        // 获取上传凭证
        $this->getUploadToken($bucket);

        // 初始化上传管理器
        $uploadMgr = new UploadManager();

        // 设置默认MIME类型
        $mimeType = $mimeType ?? 'application/octet-stream';

        // 上传文件
        list($result, $error) = $uploadMgr->putFile(
            $this->uploadToken,
            $savePath,
            $filePath,
            null,
            $mimeType,
            $checkCrc,
            null,
            'v2'
        );

        if ($error !== null) {
            throw new CommonException('QiNiuUploadFileError');
        }

        return $result;
    }

    /**
     * 上传文件内容(二进制数据)
     *
     * @param string $data 文件内容
     * @param string $bucket 存储空间名称
     * @param string $savePath 保存路径(文件key)
     * @param string|null $mimeType MIME类型
     * @return array 返回上传结果
     * @throws CommonException
     */
    public function uploadData(string $data, string $bucket, string $savePath, ?string $mimeType = null): array
    {
        if (empty($data)) {
            throw new CommonException('QiNiuUploadDataEmpty');
        }

        // 获取上传凭证
        $this->getUploadToken($bucket);

        // 初始化上传管理器
        $uploadMgr = new UploadManager();

        // 设置默认MIME类型
        $mimeType = $mimeType ?? 'application/octet-stream';

        // 上传数据
        list($result, $error) = $uploadMgr->put(
            $this->uploadToken,
            $savePath,
            $data,
            null,
            $mimeType
        );

        if ($error !== null) {
            throw new CommonException('QiNiuUploadDataError');
        }

        return $result;
    }

    /**
     * 获取私有空间下载链接
     *
     * @param string $savePath 保存路径(文件key)
     * @param int|null $expires 有效期(秒),不传则使用默认值3600秒
     * @return string 签名后的下载链接
     * @throws CommonException
     */
    public function getPrivateFileUrl(string $savePath, ?int $expires = null): string
    {
        if (empty($this->cdnUrl)) {
            throw new CommonException('QiNiuCdnUrlError');
        }

        if (empty($savePath)) {
            throw new CommonException('QiNiuFilePathEmpty');
        }

        // 构造基础URL
        $baseUrl = $this->cdnUrl . $savePath;

        // 对链接进行签名
        $expires = $expires ?? 3600;
        $signedUrl = $this->auth->privateDownloadUrl($baseUrl, $expires);

        return $signedUrl;
    }

    /**
     * 获取公有空间下载链接
     *
     * @param string $savePath 保存路径(文件key)
     * @return string 下载链接
     * @throws CommonException
     */
    public function getPublicFileUrl(string $savePath): string
    {
        if (empty($this->cdnUrl)) {
            throw new CommonException('QiNiuCdnUrlError');
        }

        if (empty($savePath)) {
            throw new CommonException('QiNiuFilePathEmpty');
        }

        // 构造URL
        $baseUrl = $this->cdnUrl . $savePath;

        return $baseUrl;
    }

    /**
     * 删除文件
     *
     * @param string $bucket 存储空间名称
     * @param string $savePath 文件key
     * @return bool 删除成功返回true
     * @throws CommonException
     */
    public function deleteFile(string $bucket, string $savePath): bool
    {
        if (empty($bucket)) {
            throw new CommonException('QiNiuBucketError');
        }

        if (empty($savePath)) {
            throw new CommonException('QiNiuFilePathEmpty');
        }

        // 初始化空间管理器
        $bucketMgr = new BucketManager($this->auth);

        // 删除文件
        $error = $bucketMgr->delete($bucket, $savePath);

        if ($error !== null) {
            throw new CommonException('QiNiuDeleteFileError');
        }

        return true;
    }

    /**
     * 获取文件信息
     *
     * @param string $bucket 存储空间名称
     * @param string $savePath 文件key
     * @return array 文件信息
     * @throws CommonException
     */
    public function getFileInfo(string $bucket, string $savePath): array
    {
        if (empty($bucket)) {
            throw new CommonException('QiNiuBucketError');
        }

        if (empty($savePath)) {
            throw new CommonException('QiNiuFilePathEmpty');
        }

        // 初始化空间管理器
        $bucketMgr = new BucketManager($this->auth);

        // 获取文件信息
        list($fileInfo, $error) = $bucketMgr->stat($bucket, $savePath);

        if ($error !== null) {
            throw new CommonException('QiNiuGetFileInfoError');
        }

        return $fileInfo;
    }

    /**
     * 获取上传凭证(供客户端使用)
     *
     * @param string $bucket 存储空间名称
     * @param string|null $keyToOverwrite 覆盖上传的文件名
     * @return string 上传凭证
     * @throws CommonException
     */
    public function getUploadTokenForClient(string $bucket, ?string $keyToOverwrite = null): string
    {
        return $this->getUploadToken($bucket, $keyToOverwrite);
    }
}

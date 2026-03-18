<?php

/*
 * @Description: Elasticsearch服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-15 23:49:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-19 04:34:18
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Tool\youhujun\php-tool-youhujun\src\App\Facades\V1\Es\EsFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facades\V1\Es;

use YouHuJun\Tool\App\Services\V1\Es\EsFacadeService;
use YouHuJun\Tool\App\Services\V1\Es\Contracts\EsDataSyncContract;
use BadMethodCallException;

/**
 * Elasticsearch服务静态门面类
 *
 * 提供静态方法调用Elasticsearch服务
 * 所有方法都有完整的PHPDoc注释,支持IDE代码提示和自动补全
 *
 * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService
 */
class EsFacade
{
    /**
     * 服务实例(单例)
     *
     * @var EsFacadeService|null
     */
    private static ?EsFacadeService $instance = null;

    /**
     * 初始化ES服务
     *
     * @param string $esHost ES服务地址,如http://127.0.0.1:9200
     * @param string|null $esUser ES认证账号
     * @param string|null $esPass ES认证密码
     * @return EsFacadeService
     */
    public static function init(string $esHost = 'http://127.0.0.1:9200', ?string $esUser = null, ?string $esPass = null): EsFacadeService
    {
        if (!self::$instance) {
            self::$instance = new EsFacadeService($esHost, $esUser, $esPass);
        }
        return self::$instance;
    }

    /**
     * 获取服务实例
     *
     * @return EsFacadeService
     */
    private static function getInstance(): EsFacadeService
    {
        if (self::$instance === null) {
            self::$instance = self::init();
        }

        return self::$instance;
    }

    /**
     * 检查索引是否存在
     *
     * @param string $index 索引名
     * @return bool 存在返回true，否则false
     * @see EsFacadeService::indexExists()
     */
    public static function indexExists(string $index): bool
    {
        return self::getInstance()->indexExists($index);
    }

    /**
     * 创建 Elasticsearch 索引
     *
     * 通过 HTTP PUT 请求向 Elasticsearch 服务器发送索引创建请求。
     * 如果索引已存在，也会返回成功状态。
     *
     * @param string $index 索引名称
     * @param array $body 索引配置和映射信息，默认为空数组
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'error'=>...]
     * @see EsFacadeService::createIndex()
     */
    public static function createIndex(string $index, array $body = []): array
    {
        return self::getInstance()->createIndex($index, $body);
    }

    /**
     * 更新 ES 索引映射（新增字段，不删数据、不影响原有字段）
     * 相当于 MySQL 的 ALTER TABLE ADD COLUMN
     *
     * @param string $index 索引名称
     * @param array $newFields 新增字段的映射配置，格式：['字段名' => ['type' => '字段类型']]
     * @return array $result 返回数组信息
     */
    public static function updateMapping(string $index, array $newFields): array
    {
        return self::getInstance()->updateMapping($index, $newFields);
    }

    /**
     * 删除索引
     *
     * @param string $index 索引名
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'error'=>...]
     * @see EsFacadeService::deleteIndex()
     */
    public static function deleteIndex(string $index): array
    {
        return self::getInstance()->deleteIndex($index);
    }

    /**
     * 创建文档
     *
     * @param string $index 索引名称
     * @param array $data 文档数据
     * @param string|null $docId 文档ID，为null时自动生成
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'error'=>...]
     * @see EsFacadeService::createDoc()
     */
    public static function createDoc(string $index, array $data, ?string $docId = null): array
    {
        return self::getInstance()->createDoc($index, $data, $docId);
    }

    /**
     * 获取单个文档
     *
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'data'=>[...],'error'=>...]
     * @see EsFacadeService::findDoc()
     */
    public static function findDoc(string $index, string $docId): array
    {
        return self::getInstance()->findDoc($index, $docId);
    }

    /**
     * 更新文档（全量/局部）
     *
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @param array $data 更新数据（数组）
     * @param bool $isPartial 是否局部更新（true=局部，false=全量）
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'data'=>[...],'error'=>...]
     * @see EsFacadeService::updateDoc()
     */
    public static function updateDoc(string $index, string $docId, array $data, bool $isPartial = true): array
    {
        return self::getInstance()->updateDoc($index, $docId, $data, $isPartial);
    }

    /**
     * 删除单个文档
     *
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'error'=>...]
     * @see EsFacadeService::deleteDoc()
     */
    public static function deleteDoc(string $index, string $docId): array
    {
        return self::getInstance()->deleteDoc($index, $docId);
    }

    /**
     * 按条件搜索文档
     *
     * @param string $index 索引名（多个用逗号分隔，如：index1,index2）
     * @param array $query ES查询条件（如：['match' => ['title' => '游鹄生态']]）
     * @param int $from 起始位置（分页）
     * @param int $size 返回数量（默认10）
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'data'=>[...],'error'=>...]
     * @see EsFacadeService::searchDoc()
     */
    public static function searchDoc(string $index, array $query, int $from = 0, int $size = 10): array
    {
        return self::getInstance()->searchDoc($index, $query, $from, $size);
    }

    /**
     * 按条件删除文档
     *
     * @param string $index 索引名
     * @param array $query 删除条件
     * @return array 返回数组信息 ['code'=>0,'msg'=>'成功','status'=>1,'error'=>...]
     * @see EsFacadeService::deleteByQuery()
     */
    public static function deleteByQuery(string $index, array $query): array
    {
        return self::getInstance()->deleteByQuery($index, $query);
    }

    /**
     * 批量写入/更新文档（直接调用ES _bulk API）
     *
     * @param string $index 索引名称
     * @param array $data 批量数据（每条含 _docId 字段）
     * @return array 统一格式的操作结果 ['code'=>0,'msg'=>'成功','status'=>1,'error'=>...]
     * @see EsFacadeService::batchActDoc()
     */
    public static function batchActDoc(string $index, array $data): array
    {
        return self::getInstance()->batchActDoc($index, $data);
    }

    /**
     * 批量删除文档（支持单ID/多ID/条件删除）
     *
     * @param string $index 索引名称
     * @param string|array $dataOrCondition 删除条件：
     *                                     - 字符串：单个文档ID
     *                                     - 数组（一维）：多个文档ID ['1','2','3']
     *                                     - 数组（二维）：ES查询条件 ['match' => ['title' => '测试']]
     * @return array 统一格式的操作结果 ['code'=>0,'msg'=>'成功','status'=>1,'error'=>...]
     * @see EsFacadeService::batchDeleteDoc()
     */
    public static function batchDeleteDoc(string $index, string|array $dataOrCondition): array
    {
        return self::getInstance()->batchDeleteDoc($index, $dataOrCondition);
    }

    /**
     * 自定义ES请求（兼容特殊操作）
     *
     * @param string $method 请求方法（GET/POST/PUT/DELETE）
     * @param string $path 请求路径（如：/_cat/indices）
     * @param array $data 请求数据（可选）
     * @return array 响应结果
     * @see EsFacadeService::customRequest()
     */
    public static function customRequest(string $method, string $path, array $data = []): array
    {
        return self::getInstance()->customRequest($method, $path, $data);
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

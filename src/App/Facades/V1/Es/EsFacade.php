<?php

/*
 * @Description: Elasticsearch服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-15 23:49:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-08-11 16:09:44
 * @FilePath: \youhu-laravel-api-13d:\wwwroot\PHP\Components\Tool\youhujun\php-tool-youhujun\src\App\Facades\V1\Es\EsFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facades\V1\Es;
//注解
use App\Attributes\Common\DocNote;
use App\Attributes\Common\DocParams;
use YouHuJun\Tool\App\Services\V1\Es\EsFacadeService;
use BadMethodCallException;

/**
 * Elasticsearch服务静态门面类
 *
 * 提供静态方法调用Elasticsearch服务
 * 所有方法都有完整的PHPDoc注释,支持IDE代码提示和自动补全
 *
 * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService
 */
#[DocNote('Elasticsearch服务静态门面')]
class EsFacade
{
    
    #[DocNote('服务实例（单例）')]
    private static ?EsFacadeService $instance = null;

    
    #[DocParams('初始化ES服务', ['esHost' => ['type' => 'string', 'note' => 'ES服务地址（如http://127.0.0.1:9200）'], 'esUser' => ['type' => 'string|null', 'note' => 'ES认证账号'], 'esPass' => ['type' => 'string|null', 'note' => 'ES认证密码'], 'return' => ['type' => 'EsFacadeService', 'note' => '返回EsFacadeService实例']])]
    public static function init(string $esHost = 'http://127.0.0.1:9200', ?string $esUser = null, ?string $esPass = null): EsFacadeService
    {
        if (!self::$instance) {
            self::$instance = new EsFacadeService($esHost, $esUser, $esPass);
        }
        return self::$instance;
    }

    
    #[DocParams('获取服务实例', ['return' => ['type' => 'EsFacadeService']])]
    private static function getInstance(): EsFacadeService
    {
        if (self::$instance === null) {
            self::$instance = self::init();
        }

        return self::$instance;
    }

  
    #[DocParams('检查索引是否存在', ['index' => ['type' => 'string', 'note' => '索引名'], 'return' => ['type' => 'bool', 'note' => '存在返回true，否则false']])]
    public static function indexExists(string $index): bool
    {
        return self::getInstance()->indexExists($index);
    }

    
    #[DocParams('创建Elasticsearch索引', ['index' => ['type' => 'string', 'note' => '索引名称'], 'body' => ['type' => 'array', 'note' => '索引配置和映射信息'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function createIndex(string $index, array $body = []): array
    {
        return self::getInstance()->createIndex($index, $body);
    }

   
    #[DocParams('更新ES索引映射（新增字段，不删数据）', ['index' => ['type' => 'string', 'note' => '索引名称'], 'newFields' => ['type' => 'array', 'note' => '新增字段的映射配置'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function updateMapping(string $index, array $newFields): array
    {
        return self::getInstance()->updateMapping($index, $newFields);
    }

   
    #[DocParams('删除索引', ['index' => ['type' => 'string', 'note' => '索引名'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function deleteIndex(string $index): array
    {
        return self::getInstance()->deleteIndex($index);
    }

   
    #[DocParams('创建文档', ['index' => ['type' => 'string', 'note' => '索引名称'], 'data' => ['type' => 'array', 'note' => '文档数据'], 'docId' => ['type' => 'string|null', 'note' => '文档ID，为null时自动生成'], 'refresh' => ['type' => 'bool', 'note' => '默认开启强制刷新'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function createDoc(string $index, array $data, ?string $docId = null,bool $refresh = true): array
    {
        return self::getInstance()->createDoc($index, $data, $docId);
    }

    
    #[DocParams('获取单个文档', ['index' => ['type' => 'string', 'note' => '索引名'], 'docId' => ['type' => 'string', 'note' => '文档ID'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function findDoc(string $index, string $docId): array
    {
        return self::getInstance()->findDoc($index, $docId);
    }

  
    #[DocParams('更新文档（全量/局部）', ['index' => ['type' => 'string', 'note' => '索引名'], 'docId' => ['type' => 'string', 'note' => '文档ID'], 'data' => ['type' => 'array', 'note' => '更新数据'], 'isPartial' => ['type' => 'bool', 'note' => '是否局部更新（true=局部，false=全量）'], 'refresh' => ['type' => 'bool', 'note' => '默认开启强制刷新'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function updateDoc(string $index, string $docId, array $data, bool $isPartial = true,bool $refresh = true): array
    {
        return self::getInstance()->updateDoc($index, $docId, $data, $isPartial);
    }

    
    #[DocParams('删除单个文档', ['index' => ['type' => 'string', 'note' => '索引名'], 'docId' => ['type' => 'string', 'note' => '文档ID'], 'refresh' => ['type' => 'bool', 'note' => '默认开启强制刷新'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function deleteDoc(string $index, string $docId,bool $refresh = true): array
    {
        return self::getInstance()->deleteDoc($index, $docId);
    }

    
    #[DocParams('按条件搜索文档', ['index' => ['type' => 'string', 'note' => '索引名（多个用逗号分隔）'], 'query' => ['type' => 'array', 'note' => 'ES查询条件'], 'from' => ['type' => 'int', 'note' => '起始位置（分页）'], 'size' => ['type' => 'int', 'note' => '返回数量（默认10）'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function searchDoc(string $index, array $query, int $from = 0, int $size = 10): array
    {
        return self::getInstance()->searchDoc($index, $query, $from, $size);
    }

   
    #[DocParams('按条件删除文档', ['index' => ['type' => 'string', 'note' => '索引名'], 'query' => ['type' => 'array', 'note' => '删除条件'], 'refresh' => ['type' => 'bool', 'note' => '默认开启强制刷新'], 'return' => ['type' => 'array', 'note' => '返回数组信息']])]
    public static function deleteByQuery(string $index, array $query,bool $refresh = true): array
    {
        return self::getInstance()->deleteByQuery($index, $query);
    }

   
    #[DocParams('批量写入/更新文档', ['index' => ['type' => 'string', 'note' => '索引名称'], 'data' => ['type' => 'array', 'note' => '批量数据（每条含_docId字段）'], 'refresh' => ['type' => 'bool', 'note' => '默认开启强制刷新'], 'return' => ['type' => 'array', 'note' => '统一格式的操作结果']])]
    public static function batchActDoc(string $index, array $data,bool $refresh = true): array
    {
        return self::getInstance()->batchActDoc($index, $data);
    }

    
    #[DocParams('清空当前索引内全部文档', ['index' => ['type' => 'string', 'note' => '索引名称'], 'refresh' => ['type' => 'bool', 'note' => '强制刷新'], 'return' => ['type' => 'array', 'note' => '统一格式的操作结果']])]
    public static function clearAllDoc(string $index, bool $refresh = true): array
    {
        return self::getInstance()->clearAllDoc($index, $refresh);
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
     * @param bool $refresh 默认开启强制刷新
     * @see EsFacadeService::batchDeleteDoc()
     */
    #[DocParams('批量删除文档（支持单ID/多ID/条件删除）', ['index' => ['type' => 'string', 'note' => '索引名称'], 'dataOrCondition' => ['type' => 'string|array', 'note' => '删除条件（单ID/多ID数组/查询条件）'], 'refresh' => ['type' => 'bool', 'note' => '默认开启强制刷新'], 'return' => ['type' => 'array', 'note' => '统一格式的操作结果']])]
    public static function batchDeleteDoc(string $index, string|array $dataOrCondition,bool $refresh = true): array
    {
        return self::getInstance()->batchDeleteDoc($index, $dataOrCondition);
    }

   
    #[DocParams('自定义ES请求（兼容特殊操作）', ['method' => ['type' => 'string', 'note' => '请求方法（GET/POST/PUT/DELETE）'], 'path' => ['type' => 'string', 'note' => '请求路径（如：/_cat/indices）'], 'data' => ['type' => 'array', 'note' => '请求数据'], 'refresh' => ['type' => 'bool', 'note' => '默认开启强制刷新'], 'return' => ['type' => 'array', 'note' => '响应结果']])]
    public static function customRequest(string $method, string $path, array $data = [],bool $refresh = true): array
    {
        return self::getInstance()->customRequest($method, $path, $data);
    }

    
    #[DocParams('动态调用未在Facade中显式声明的方法', ['method' => ['type' => 'string', 'note' => '方法名'], 'parameters' => ['type' => 'array', 'note' => '参数数组'], 'return' => ['type' => 'mixed']])]
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

<?php
/*
 * @Description: Elasticsearch服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-15 23:49:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-16 03:19:41
 * @FilePath: \php-tool-youhujun\src\App\Facades\V1\Es\EsFacade.php
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
     * @return EsFacadeService
     */
    public static function init(string $esHost = 'http://127.0.0.1:9200',string $esUser = null, string $esPass = null): EsFacadeService
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
     * 创建/新增文档(PUT/POST)
     *
     * @param string $index 索引名
     * @param array $data 文档数据(数组)
     * @param string|null $docId 文档ID(不传则ES自动生成)
     * @return array 响应结果(解析为数组)
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::createDoc()
     */
    public static function createDoc(string $index, array $data, string $docId = null): array
    {
        return self::getInstance()->createDoc($index, $data, $docId);
    }

    /**
     * 获取单个文档
     *
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @return array 响应结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::getDoc()
     */
    public static function getDoc(string $index, string $docId): array
    {
        return self::getInstance()->getDoc($index, $docId);
    }

    /**
     * 更新文档(全量/局部)
     *
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @param array $data 更新数据(数组)
     * @param bool $isPartial 是否局部更新(true=局部,false=全量)
     * @return array 响应结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::updateDoc()
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
     * @return array 响应结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::deleteDoc()
     */
    public static function deleteDoc(string $index, string $docId): array
    {
        return self::getInstance()->deleteDoc($index, $docId);
    }

    /**
     * 按条件搜索文档
     *
     * @param string $index 索引名(多个用逗号分隔,如:index1,index2)
     * @param array $query ES查询条件(如:['match' => ['title' => '游鹄生态']])
     * @param int $from 起始位置(分页)
     * @param int $size 返回数量(默认10)
     * @return array 搜索结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::searchDoc()
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
     * @return array 响应结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::deleteByQuery()
     */
    public static function deleteByQuery(string $index, array $query): array
    {
        return self::getInstance()->deleteByQuery($index, $query);
    }

    /**
     * 检查索引是否存在
     *
     * @param string $index 索引名
     * @return bool 存在返回true,否则false
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::indexExists()
     */
    public static function indexExists(string $index): bool
    {
        return self::getInstance()->indexExists($index);
    }

    /**
     * 创建索引(带映射配置)
     *
     * @param string $index 索引名
     * @param array $mapping 索引映射配置(可选)
     * @return array 响应结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::createIndex()
     */
    public static function createIndex(string $index, array $mapping = []): array
    {
        return self::getInstance()->createIndex($index, $mapping);
    }

    /**
     * 删除索引
     *
     * @param string $index 索引名
     * @return array 响应结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::deleteIndex()
     */
    public static function deleteIndex(string $index): array
    {
        return self::getInstance()->deleteIndex($index);
    }

    /**
     * 自定义ES请求(兼容特殊操作)
     *
     * @param string $method 请求方法(GET/POST/PUT/DELETE)
     * @param string $path 请求路径(如:/_cat/indices)
     * @param array $data 请求数据(可选)
     * @return array 响应结果
     * @throws \Exception
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::customRequest()
     */
    public static function customRequest(string $method, string $path, array $data = []): array
    {
        return self::getInstance()->customRequest($method, $path, $data);
    }

    /**
     * 注册数据同步钩子(闭包方式)
     *
     * @param \Closure $hook 同步钩子(接收 index/data/docId 参数)
     * @return EsFacadeService
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::registerSyncHook()
     */
    public static function registerSyncHook(\Closure $hook): EsFacadeService
    {
        return self::getInstance()->registerSyncHook($hook);
    }

    /**
     * 注册数据同步接口实现(接口方式)
     *
     * @param EsDataSyncContract $contract 同步接口实现类
     * @return EsFacadeService
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::registerSyncContract()
     */
    public static function registerSyncContract(EsDataSyncContract $contract): EsFacadeService
    {
        return self::getInstance()->registerSyncContract($contract);
    }

    /**
     * 执行数据同步(自动适配钩子/接口方式)
     *
     * @param string $type 同步类型:single/batch/delete
     * @param string $index 索引名
     * @param array $data 同步数据
     * @param string|null $docId 文档ID(仅单条同步用)
     * @return array 同步结果
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     *
     * @see \YouHuJun\Tool\App\Services\V1\Es\EsFacadeService::syncData()
     */
    public static function syncData(string $type, string $index, array $data, string $docId = null): array
    {
        return self::getInstance()->syncData($type, $index, $data, $docId);
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

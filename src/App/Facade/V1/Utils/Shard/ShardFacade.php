<?php
/*
 * @Descripttion: 游鹄系统全局分片门面类
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-23 12:51:24
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 12:51:24
 * @FilePath: \src\App\Facade\V1\Utils\Shard\ShardFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */
namespace YouHuJun\Tool\App\Facade\V1\Utils\Shard;

use YouHuJun\Tool\App\Service\V1\Utils\Shard\ShardFacadeService;
use BadMethodCallException;

class ShardFacade
{
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    public static function setInstance(ShardFacadeService $instance): void
    {
        static::$instance = $instance;
    }

    public static function clearInstance(): void
    {
        static::$instance = null;
    }

    /**
     * 设置分片配置
     *
     * @param array $config 配置数组
     * @return void
     */
    public static function setConfig(array $config): void
    {
        self::getInstance()->setConfig($config);
    }

    /**
     * 获取配置值
     *
     * @param string $key 配置键
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getConfig(string $key, mixed $default = null): mixed
    {
        return self::getInstance()->getConfig($key, $default);
    }

    /**
     * 全局统一:计算分片信息
     *
     * @param string|int $userUid 用户UID(所有模块的核心分片依据)
     * @return array [db_name, table_no, shard_key]
     *
     * 示例:
     * $info = ShardFacade::calc(123456);
     * // 返回: ['db' => 'ds_0', 'table_no' => 0, 'shard_key' => 0]
     */
    public static function calc(string|int $userUid): array
    {
        return self::getInstance()->calc($userUid);
    }

    /**
     * 获取分片表名
     *
     * @param string|int $userUid 用户UID
     * @param string $baseTable 基础表名(如users/order/feed)
     * @return string 完整表名
     *
     * 示例:
     * $tableName = ShardFacade::getTableName(123456, 'users');
     * // 返回: 'users_0'
     */
    public static function getTableName(string|int $userUid, string $baseTable): string
    {
        return self::getInstance()->getTableName($userUid, $baseTable);
    }

    /**
     * 获取分片数据库连接名
     *
     * @param string|int $userUid 用户UID
     * @return string 数据库名
     *
     * 示例:
     * $dbName = ShardFacade::getDbName(123456);
     * // 返回: 'ds_0'
     */
    public static function getDbName(string|int $userUid): string
    {
        return self::getInstance()->getDbName($userUid);
    }

    /**
     * 获取分片键值
     *
     * @param string|int $userUid 用户UID
     * @return int 分片键
     *
     * 示例:
     * $shardKey = ShardFacade::getShardKey(123456);
     * // 返回: 0
     */
    public static function getShardKey(string|int $userUid): int
    {
        return self::getInstance()->getShardKey($userUid);
    }

    protected static function getInstance(): ShardFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new ShardFacadeService();
        }
        return static::$instance;
    }

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

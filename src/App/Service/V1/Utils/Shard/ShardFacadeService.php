<?php
/*
 * @Descripttion: 游鹄系统全局分片工具类（一步到位,所有模块复用）
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-23 12:51:24
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 12:51:24
 * @FilePath: App\Service\V1\Utils\Shard\ShardFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Utils\Shard;

class ShardFacadeService
{
    /**
     * 分片配置
     */
    private array $config = [
        'db_count' => 1,
        'table_count' => 1,
        'db_prefix' => 'ds_',
        'default_db' => 'ds_0',
    ];

    /**
     * 构造函数
     *
     * @param array|null $config 分片配置
     */
    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 设置分片配置
     *
     * @param array $config 配置数组
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 获取配置值
     *
     * @param string $key 配置键
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * 全局统一:计算分片信息
     *
     * @param string|int $uid 业务ID(用户UID/店铺UID等业务实体ID,所有模块的核心分片依据)
     * @return array [db_name, table_no, shard_key]
     */
    public function calc(string|int $uid): array
    {
        // 1. 读取分片配置
        $dbCount = $this->config['db_count'];
        $tableCount = $this->config['table_count'];
        $dbPrefix = $this->config['db_prefix'];
        $defaultDb = $this->config['default_db'];

        // 2. 统一转成数值计算
        $uidValue = (int)$uid;

        // 3. 计算分片库/表/分片键(全局唯一规则)
        $dbNo = $uidValue % $dbCount;
        $tableNo = $uidValue % $tableCount;
        $shardKey = $tableNo; // shard_key = uid % table_count

        // 4. 拼接库名
        $dbName = $dbPrefix . $dbNo;

        return [
            'db' => $dbName,
            'table_no' => $tableNo,
            'shard_key' => $shardKey
        ];
    }

    /**
     * 获取分片表名
     *
     * @param string|int $uid 业务ID(用户UID/店铺UID等业务实体ID)
     * @param string $baseTable 基础表名(如users/order/feed/shop)
     * @return string 完整表名
     */
    public function getTableName(string|int $uid, string $baseTable): string
    {
        $calc = $this->calc($uid);
        return $baseTable . '_' . $calc['table_no'];
    }

    /**
     * 获取分片数据库连接名
     *
     * @param string|int $uid 业务ID(用户UID/店铺UID等业务实体ID)
     * @return string 数据库名
     */
    public function getDbName(string|int $uid): string
    {
        $calc = $this->calc($uid);
        return $calc['db'];
    }

    /**
     * 获取分片键值
     *
     * @param string|int $uid 业务ID(用户UID/店铺UID等业务实体ID)
     * @return int 分片键
     */
    public function getShardKey(string|int $uid): int
    {
        $calc = $this->calc($uid);
        return $calc['shard_key'];
    }
}

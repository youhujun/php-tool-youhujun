<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-01-23 13:23:33
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-06 12:54:45
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Tool\youhujun\php-tool-youhujun\src\App\Services\V1\Utils\Shard\ShardFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace YouHuJun\Tool\App\Services\V1\Utils\Shard;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * 分库分表规则：
    总分片数 = 库数 × 表数
    shard_key = uid % 总分片数
    库号 = intdiv (shard_key, 表数)
    表号 = shard_key % 表数

    直接用库里存好的 shard_key
    用 intdiv(shard_key, tableCount) 算库
    用 shard_key % tableCount 算表
 */
class ShardFacadeService
{
    /**
     * 多配置池（唯一配置存储）
     */
    private static array $multiConfigPool = [];

    /**
     * 设置指定标识的分片配置
     * @param string $configKey 配置标识（如youhujun/shard_map）
     * @param array $config 分片配置
     */
    public static function setMultiConfig(string $configKey, array $config): void
    {
        self::$multiConfigPool[$configKey] = array_merge([
            'db_count' => 1,
            'table_count' => 1,
            'db_prefix' => 'ds_',
            'default_db' => 'ds_0',
        ], $config);
    }

    /**
     * 获取指定标识的配置值
     * @param string $configKey 配置标识
     * @param string $key 配置项
     * @param mixed $default 默认值
     */
    public static function getMultiConfig(string $configKey, string $key, mixed $default = null): mixed
    {
        // 开发阶段：配置不存在直接报错，强制暴露问题
        if (!isset(self::$multiConfigPool[$configKey])) {
            throw new CommonException('ShardKeyEmptyError');
        }
        return self::$multiConfigPool[$configKey][$key] ?? $default;
    }

    /**
     * 计算分片信息（必须传configKey，无默认值！）
     * @param string|int $uid 业务ID
     * @param string $configKey 配置标识（强制传！）
     */
    public function calc(string|int $uid, string $configKey): array
    {
        $config = self::$multiConfigPool[$configKey] ?? [];
        if (empty($config)) {
            throw new CommonException('ShardKeyEmptyError');
        }

        $uidValue = (int)$uid;
        $dbCount = $config['db_count'];
        $tableCount = $config['table_count'];
        $dbPrefix = $config['db_prefix'];

        // 总分片
        $totalShardNumber = $dbCount * $tableCount;
        // 分片键
        $shardKey   = $uidValue % $totalShardNumber;
        // 库序号
        $dbNo       = intdiv($shardKey, $tableCount);
        // 表序号
        $tableNo    = $shardKey % $tableCount;

        return [
            'db'        => $dbPrefix . $dbNo,
            'table_no'  => $tableNo,
            'shard_key' => $shardKey,
        ];
    }

    /**
     * 所有方法都强制传configKey，无默认值！
     */
    public function getTableName(string|int $uid, string $baseTable, string $configKey): string
    {
        $calc = $this->calc($uid, $configKey);
        return $baseTable . '_' . $calc['table_no'];
    }

    public function getDbName(string|int $uid, string $configKey): string
    {
        $calc = $this->calc($uid, $configKey);
        return $calc['db'];
    }

    public function getShardKey(string|int $uid, string $configKey): int
    {
        $calc = $this->calc($uid, $configKey);
        return $calc['shard_key'];
    }
}

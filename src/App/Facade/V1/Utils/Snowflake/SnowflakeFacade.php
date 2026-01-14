<?php
/*
 * @Descripttion: 雪花算法门面类 - 提供静态方法调用
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-14 16:36:58
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-14 16:41:02
 * @FilePath: \src\App\Facade\V1\Utils\Snowflake\SnowflakeFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */
namespace YouHuJun\Tool\App\Facade\V1\Utils\Snowflake;

use YouHuJun\Tool\App\Service\V1\Utils\Snowflake\SnowflakeFacadeService;
use BadMethodCallException;

class SnowflakeFacade
{
    /**
     * 生成雪花ID
     *
     * @param int|null $machineId 机器ID，不传则默认为1，用于分布式部署时不同节点配置不同值
     * @param string|null $startTime 起始时间，格式：Y-m-d H:i:s，不传则使用当天零点
     * @return string 返回雪花ID字符串，避免大整数溢出
     *
     * 示例:
     * SnowflakeFacade::generate();                                      // 使用默认配置（machineId=1,起始时间=当天零点）
     * SnowflakeFacade::generate(2);                                     // 指定machineId
     * SnowflakeFacade::generate(2, '2025-06-01 00:00:00');              // 指定machineId和起始时间
     */
    public static function generate(?int $machineId = null, ?string $startTime = null): string
    {
        return self::getInstance()->generate($machineId, $startTime);
    }

    /**
     * 生成雪花ID（别名方法）
     *
     * @param int|null $machineId 机器ID，不传则默认为1
     * @param string|null $startTime 起始时间，格式：Y-m-d H:i:s，不传则使用当天零点
     * @return string
     */
    public static function id(?int $machineId = null, ?string $startTime = null): string
    {
        return self::getInstance()->generate($machineId, $startTime);
    }

    /**
     * 解析雪花ID
     *
     * @param string|int $id 雪花ID
     * @param int|null $machineId 机器ID，需要与生成时一致
     * @param string|null $startTime 起始时间，格式：Y-m-d H:i:s，需要与生成时一致
     * @return array 返回包含时间戳、机器ID、序列号等信息的数组
     *
     * 示例:
     * SnowflakeFacade::parse(1234567890123456789, 1, '2025-06-01 00:00:00');
     */
    public static function parse(string|int $id, ?int $machineId = null, ?string $startTime = null): array
    {
        return self::getInstance()->parse($id, $machineId, $startTime);
    }

    /**
     * 设置Service实例（用于测试或自定义实现）
     *
     * @param SnowflakeFacadeService $instance
     * @return void
     */
    public static function setInstance(SnowflakeFacadeService $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * 清除实例
     *
     * @return void
     */
    public static function clearInstance(): void
    {
        static::$instance = null;
    }

    /**
     * 获取Service实例
     *
     * @return SnowflakeFacadeService
     */
    protected static function getInstance(): SnowflakeFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new SnowflakeFacadeService();
        }
        return static::$instance;
    }

    /**
     * 动态调用未定义的静态方法
     *
     * @param string $method 方法名
     * @param array $parameters 参数
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

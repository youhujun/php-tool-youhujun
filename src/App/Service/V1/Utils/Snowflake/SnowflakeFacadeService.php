<?php
/*
 * @Descripttion: 雪花算法服务类 - 生成分布式唯一ID
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-14 16:36:58
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-14 16:36:58
 * @FilePath: App\Service\V1\Utils\Snowflake\SnowflakeFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Utils\Snowflake;

use Godruoyi\Snowflake\Snowflake;

class SnowflakeFacadeService
{
    /**
     * 生成雪花ID（返回int类型）
     *
     * @param int|null $machineId 机器ID，不传则默认为1
     * @param string|null $startTime 起始时间，格式：Y-m-d H:i:s，不传则使用当天零点
     * @return int
     */
    public function generate(?int $machineId = null, ?string $startTime = null): int
    {
        $snowflake = $this->createSnowflake($machineId, $startTime);
        return (int) $snowflake->id();
    }

    /**
     * 生成雪花ID（int类型，别名方法）
     *
     * @param int|null $machineId 机器ID，不传则默认为1
     * @param string|null $startTime 起始时间，格式：Y-m-d H:i:s，不传则使用当天零点
     * @return int
     */
    public function id(?int $machineId = null, ?string $startTime = null): int
    {
        return $this->generate($machineId, $startTime);
    }

    /**
     * 解析雪花ID（返回详细信息）
     *
     * @param string|int $id 雪花ID
     * @param int|null $machineId 机器ID，可选参数
     * @param string|null $startTime 起始时间，格式：Y-m-d H:i:s，可选参数
     * @return array
     */
    public function parse(string|int $id, ?int $machineId = null, ?string $startTime = null): array
    {
        $snowflake = $this->createSnowflake($machineId, $startTime);
        return $snowflake->parse((string) $id);
    }

    /**
     * 创建雪花算法实例
     *
     * @param int|null $machineId 机器ID，不传则默认为1
     * @param string|null $startTime 起始时间，格式：Y-m-d H:i:s，不传则使用当天零点
     * @return Snowflake
     */
    private function createSnowflake(?int $machineId = null, ?string $startTime = null): Snowflake
    {
        $machineId = $machineId ?? 1;
        $startTimeStamp = $startTime !== null
            ? strtotime($startTime) * 1000
            : strtotime(date('Y-m-d')) * 1000;

        $snowflake = new Snowflake($machineId);
        $snowflake->setStartTimeStamp($startTimeStamp);

        return $snowflake;
    }
}

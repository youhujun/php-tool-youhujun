<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-16 00:27:52
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-16 00:29:31
 * @FilePath: \php-tool-youhujun\src\App\Services\V1\Es\Contracts\EsDataSyncContract.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace YouHuJun\Tool\App\Services\V1\Es\Contracts;

/**
 * ES数据同步契约
 * 定义数据同步的核心行为，适配钩子/接口两种同步方式
 */
interface EsDataSyncContract
{
    /**
     * 同步单条数据到ES
     * @param string $index ES索引名
     * @param array $data 同步数据（数组）
     * @param string|null $docId ES文档ID（可选）
     * @return array 同步结果
     * @throws \Exception
     */
    public function syncSingle(string $index, array $data, string $docId = null): array;

    /**
     * 批量同步数据到ES
     * @param string $index ES索引名
     * @param array $dataList 批量数据（二维数组）
     * @return array 同步结果
     * @throws \Exception
     */
    public function syncBatch(string $index, array $dataList): array;

    /**
     * 从ES删除指定数据
     * @param string $index ES索引名
     * @param string|array $condition 删除条件（docId 或 查询条件数组）
     * @return array 删除结果
     * @throws \Exception
     */
    public function syncDelete(string $index, string|array $condition): array;
}
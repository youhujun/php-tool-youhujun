<?php
/*
 * @Descripttion: 自动生成的服务类
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-03-15 23:49:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-16 03:18:47
 * @FilePath: \php-tool-youhujun\src\App\Services\V1\Es\EsFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Services\V1\Es;

use YouHuJun\Tool\App\Services\V1\Es\Contracts\EsDataSyncContract;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * @see \YouHuJun\Tool\App\Facades\V1\Es\EsFacade
 */
class EsFacadeService
{
	 /**
     * ES服务地址（如：http://127.0.0.1:9200）
     * @var string
     */
    private $esHost;

    /**
     * 请求头（默认JSON格式）
     * @var array
     */
    private $headers;

	/**
     * ES认证账号
     * @var string|null
     */
    private ?string $esUser = null;

    /**
     * ES认证密码
     * @var string|null
     */
    private ?string $esPass = null;

	/**
     * 数据同步钩子（闭包）
     * @var \Closure|null
     */
    private ?\Closure $syncHook = null;

    /**
     * 数据同步接口实现类实例
     * @var EsDataSyncContract|null
     */
    private ?EsDataSyncContract $syncContract = null;

    /**
     * 构造函数（初始化ES配置）
     * @param string $esHost ES服务地址
     */
    public function __construct(string $esHost = 'http://127.0.0.1:9200',string $esUser = null, string $esPass = null)
    {
		// 去除末尾斜杠，避免URL拼接错误
        $this->esHost = rtrim($esHost, '/');
        // 基础请求头
        $this->headers = [
            'Content-Type:application/json',
            'charset=utf-8'
        ];

        //如果有账号密码，添加Basic Auth认证头
        if ($esUser && $esPass) {
            $this->esUser = $esUser;
            $this->esPass = $esPass;
            $auth = base64_encode("{$esUser}:{$esPass}");
            $this->headers[] = "Authorization: Basic {$auth}";
        }
    }

    /**
     * 创建/新增文档（PUT/POST）
     * @param string $index 索引名
     * @param mixed $data 文档数据（数组）
     * @param string|null $docId 文档ID（不传则ES自动生成）
     * @return array 响应结果（解析为数组）
     * @throws \Exception
     */
    public function createDoc(string $index, array $data, string $docId = null): array
    {
        // 拼接URL
        if ($docId) {
            // 指定ID创建（PUT）
            $url = "{$this->esHost}/{$index}/_doc/{$docId}";
            $response = httpPut($url, $this->headers, $data);
        } else {
            // 自动生成ID（POST）
            $url = "{$this->esHost}/{$index}/_doc";
            $response = httpPost($url, $this->headers, $data);
        }

        return $this->parseResponse($response);
    }

    /**
     * 获取单个文档
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @return array 响应结果
     * @throws \Exception
     */
    public function getDoc(string $index, string $docId): array
    {
        $url = "{$this->esHost}/{$index}/_doc/{$docId}";
        $response = httpGet($url, $this->headers);
        return $this->parseResponse($response);
    }

    /**
     * 更新文档（全量/局部）
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @param array $data 更新数据（数组）
     * @param bool $isPartial 是否局部更新（true=局部，false=全量）
     * @return array 响应结果
     * @throws \Exception
     */
    public function updateDoc(string $index, string $docId, array $data, bool $isPartial = true): array
    {
        if ($isPartial) {
            // 局部更新（ES推荐方式）
            $url = "{$this->esHost}/{$index}/_update/{$docId}";
            $postData = ['doc' => $data];
        } else {
            // 全量替换
            $url = "{$this->esHost}/{$index}/_doc/{$docId}";
            $postData = $data;
        }

        $response = httpPut($url, $this->headers, $postData);
        return $this->parseResponse($response);
    }

    /**
     * 删除单个文档
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @return array 响应结果
     * @throws \Exception
     */
    public function deleteDoc(string $index, string $docId): array
    {
        $url = "{$this->esHost}/{$index}/_doc/{$docId}";
        $response = httpDelete($url, $this->headers);
        return $this->parseResponse($response);
    }

    /**
     * 按条件搜索文档
     * @param string $index 索引名（多个用逗号分隔，如：index1,index2）
     * @param array $query ES查询条件（如：['match' => ['title' => '游鹄生态']]）
     * @param int $from 起始位置（分页）
     * @param int $size 返回数量（默认10）
     * @return array 搜索结果
     * @throws \Exception
     */
    public function searchDoc(string $index, array $query, int $from = 0, int $size = 10): array
    {
        $url = "{$this->esHost}/{$index}/_search";
        $postData = [
            'from' => $from,
            'size' => $size,
            'query' => $query
        ];

        $response = httpPost($url, $this->headers, $postData);
        return $this->parseResponse($response);
    }

    /**
     * 按条件删除文档
     * @param string $index 索引名
     * @param array $query 删除条件
     * @return array 响应结果
     * @throws \Exception
     */
    public function deleteByQuery(string $index, array $query): array
    {
        $url = "{$this->esHost}/{$index}/_delete_by_query";
        $postData = ['query' => $query];
        $response = httpDelete($url, $this->headers, $postData);
        return $this->parseResponse($response);
    }

    /**
     * 检查索引是否存在
     * @param string $index 索引名
     * @return bool 存在返回true，否则false
     * @throws \Exception
     */
    public function indexExists(string $index): bool
    {
        try {
            $url = "{$this->esHost}/{$index}";
            httpGet($url, $this->headers);
            return true;
        } catch (\Exception $e) {
            // ES返回404表示索引不存在
            if (strpos($e->getMessage(), '404') !== false) {
                return false;
            }
            throw $e; // 其他错误正常抛出
        }
    }

    /**
     * 创建索引（带映射配置）
     * @param string $index 索引名
     * @param array $mapping 索引映射配置（可选）
     * @return array 响应结果
     * @throws \Exception
     */
    public function createIndex(string $index, array $mapping = []): array
    {
        $url = "{$this->esHost}/{$index}";
        $postData = !empty($mapping) ? ['mappings' => $mapping] : [];
        $response = httpPut($url, $this->headers, $postData);
        return $this->parseResponse($response);
    }

    /**
     * 删除索引
     * @param string $index 索引名
     * @return array 响应结果
     * @throws \Exception
     */
    public function deleteIndex(string $index): array
    {
        $url = "{$this->esHost}/{$index}";
        $response = httpDelete($url, $this->headers);
        return $this->parseResponse($response);
    }

    /**
     * 解析ES响应结果（转为数组）
     * @param string $response ES原始响应字符串
     * @return array 解析后的数组
     * @throws \Exception
     */
    private function parseResponse(string $response): array
    {
        if (empty($response)) {
            throw new CommonException('ES响应为空');
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CommonException('ES响应解析失败：' . json_last_error_msg());
        }

        // ES返回错误时抛出异常
        if (isset($result['error'])) {
            throw new CommonException("ES操作失败：{$result['error']['reason']}", $result['status']);
        }

        return $result;
    }

    /**
     * 自定义ES请求（兼容特殊操作）
     * @param string $method 请求方法（GET/POST/PUT/DELETE）
     * @param string $path 请求路径（如：/_cat/indices）
     * @param array $data 请求数据（可选）
     * @return array 响应结果
     * @throws \Exception
     */
    public function customRequest(string $method, string $path, array $data = []): array
    {
        $method = strtoupper($method);
        $url = "{$this->esHost}/" . ltrim($path, '/');

        switch ($method) {
            case 'GET':
                $response = httpGet($url, $this->headers);
                break;
            case 'POST':
                $response = httpPost($url, $this->headers, $data);
                break;
            case 'PUT':
                $response = httpPut($url, $this->headers, $data);
                break;
            case 'DELETE':
                $response = httpDelete($url, $this->headers, $data);
                break;
            default:
                throw new CommonException("不支持的请求方法：{$method}");
        }

        return $this->parseResponse($response);
    }


	 /**
     * 注册数据同步钩子（闭包方式）
     * @param \Closure $hook 同步钩子（接收 index/data/docId 参数）
     * @return $this
     */
    public function registerSyncHook(\Closure $hook): self
    {
        $this->syncHook = $hook;
        return $this;
    }

    /**
     * 注册数据同步接口实现（接口方式）
     * @param EsDataSyncContract $contract 同步接口实现类
     * @return $this
     */
    public function registerSyncContract(EsDataSyncContract $contract): self
    {
        $this->syncContract = $contract;
        return $this;
    }

	/**
     * 执行数据同步（自动适配钩子/接口方式）
     * @param string $type 同步类型：single/batch/delete
     * @param string $index 索引名
     * @param array $data 同步数据
     * @param string|null $docId 文档ID（仅单条同步用）
     * @return array 同步结果
     * @throws CommonException
     */
    public function syncData(string $type, string $index, array $data, string $docId = null): array
    {
        // 优先级：接口方式 > 钩子方式 > 默认ES原生操作
        try {
            // 1. 接口方式同步
            if ($this->syncContract) {
                switch ($type) {
                    case 'single':
                        return $this->syncContract->syncSingle($index, $data, $docId);
                    case 'batch':
                        return $this->syncContract->syncBatch($index, $data);
                    case 'delete':
                        return $this->syncContract->syncDelete($index, $docId ?? $data);
                    default:
                        throw new CommonException("不支持的同步类型：{$type}");
                }
            }

            // 2. 钩子方式同步
            if ($this->syncHook) {
                $hook = $this->syncHook;
                return $hook($type, $index, $data, $docId);
            }

            // 3. 默认ES原生操作（兜底）
            return $this->defaultSync($type, $index, $data, $docId);
        } catch (\Exception $e) {
            throw new CommonException("数据同步失败：{$e->getMessage()}", $e->getCode());
        }
    }

    /**
     * 默认ES原生同步（兜底方案）
     * @param string $type 同步类型
     * @param string $index 索引名
     * @param array $data 同步数据
     * @param string|null $docId 文档ID
     * @return array
     * @throws CommonException
     */
    private function defaultSync(string $type, string $index, array $data, string $docId = null): array
    {
        switch ($type) {
            case 'single':
                return $this->createDoc($index, $data, $docId);
            case 'batch':
                // ES批量同步（_bulk API）
                $url = "{$this->esHost}/_bulk";
                $bulkData = '';
                foreach ($data as $item) {
                    $id = $item['_id'] ?? null;
                    unset($item['_id']);
                    
                    // 批量新增/更新
                    $bulkData .= json_encode(['index' => ['_index' => $index, '_id' => $id]]) . "\n";
                    $bulkData .= json_encode($item, JSON_UNESCAPED_UNICODE) . "\n";
                }
                $response = httpPost($url, $this->headers, $bulkData);
                return $this->parseResponse($response);
            case 'delete':
                return $this->deleteDoc($index, $docId ?? $data['doc_id']);
            default:
                throw new CommonException("不支持的同步类型：{$type}");
        }
    }
}

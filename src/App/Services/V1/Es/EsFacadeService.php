<?php

/*
 * @Descripttion: 自动生成的服务类
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-03-15 23:49:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-13 11:24:51
 * @FilePath: \youhu-laravel-api-13d:\wwwroot\PHP\Components\Tool\youhujun\php-tool-youhujun\src\App\Services\V1\Es\EsFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Services\V1\Es;

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
    public function __construct(string $esHost = 'http://127.0.0.1:9200', string $esUser = null, string $esPass = null)
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
     * 检查索引是否存在
     * @param string $index 索引名
     * @return bool 存在返回true，否则false
     * @throws \Exception
     */
    public function indexExists(string $index): bool
    {
        $this->validateIndexName($index);

        $url = "{$this->esHost}/{$index}";
        $response = http_head($url, $this->headers);



        //成功
        //HTTP/1.1 200 OKX-elastic-product: Elasticsearchcontent-type: application/json; charset=UTF-8content-length: 1057
        //失败
        //HTTP/1.1 404 Not FoundX-elastic-product: Elasticsearchcontent-type: application/json; charset=UTF-8content-length: 371

        //$code = (int)substr($response, 9, 3);

        if (preg_match('/HTTP\/\d\.\d\s+(\d{3})/', $response, $matches)) {
            $code = (int)$matches[1];
        } else {
            // 响应格式异常时默认返回不存在
            return false;
        }

        return $code === 200;
    }


    /**
     * 创建 Elasticsearch 索引
     *
     * 通过 HTTP PUT 请求向 Elasticsearch 服务器发送索引创建请求。
     * 如果索引已存在，也会返回成功状态。
     *
     * @param string $index 索引名称
     * @param array $body 索引配置和映射信息，默认为空数组
     * @return array $result 返回数组信息
     */
    public function createIndex(string $index, array $body = []): array
    {
        $this->validateIndexName($index);

        $result = ['code' => 10000,'msg' => 'es索引创建失败','status' => 0,'error' => null];

        $url = "{$this->esHost}/{$index}";
        $response = http_put($url, $this->headers, $body);
        $jsonResultArray = $this->parseResponse($response);

        /**
         * 成功创建
         * Array
            (
                [acknowledged] => 1
                [shards_acknowledged] => 1
                [index] => youhu_users_index
            )
         */

        if (isset($jsonResultArray['index']) && $jsonResultArray['index'] == $index) {
            $result['code'] = 0;
            $result['msg'] = 'es索引创建成功';
            $result['status'] = 1;
        }

        /**
         * 已经存在索引
         * Array
        (
            [error] => Array
                (
                    [root_cause] => Array
                        (
                            [0] => Array
                                (
                                    [type] => resource_already_exists_exception
                                    [reason] => index [youhu_users_index/6Yq37NqeRWC9q65xvNaCTA] already exists
                                    [index_uuid] => 6Yq37NqeRWC9q65xvNaCTA
                                    [index] => youhu_users_index
                                )

                        )

                    [type] => resource_already_exists_exception
                    [reason] => index [youhu_users_index/6Yq37NqeRWC9q65xvNaCTA] already exists
                    [index_uuid] => 6Yq37NqeRWC9q65xvNaCTA
                    [index] => youhu_users_index
                )

            [status] => 400
        )
         */
        if (isset($jsonResultArray['error'])) {
            if (isset($jsonResultArray['error']['index']) && $jsonResultArray['error']['index'] == $index) {
                $result['code'] = 0;
                $result['msg'] = 'es索引创建成功';
                $result['status'] = 1;
            }
        }

        $result['error'] = $jsonResultArray;

        return $result;
    }

    /**
     * 更新 ES 索引映射（新增字段，不删数据、不影响原有字段）
     * 相当于 MySQL 的 ALTER TABLE ADD COLUMN
     *
     * @param string $index 索引名称
     * @param array $newFields 新增字段的映射配置，格式：['字段名' => ['type' => '字段类型']]
     * @return array $result 返回数组信息
     */
    public function updateMapping(string $index, array $newFields): array
    {
        $this->validateIndexName($index);

        $result = ['code' => 10000,'msg' => 'es索引映射更新失败','status' => 0,'error' => null];

        // 构造更新映射的请求体（只加新字段，不修改原有字段）
        $body = [
            'properties' => $newFields
        ];

        // ES 更新映射的固定接口：/_mapping
        $url = "{$this->esHost}/{$index}/_mapping";
        $response = http_put($url, $this->headers, $body);
        $jsonResultArray = $this->parseResponse($response);

        /**
         * 更新成功的返回格式：
         * Array
            (
                [acknowledged] => 1
            )
        */
        if (isset($jsonResultArray['acknowledged']) && $jsonResultArray['acknowledged'] == 1) {
            $result['code'] = 0;
            $result['msg'] = 'es索引映射更新成功';
            $result['status'] = 1;
        }
        // 捕获更新失败的异常信息
        $result['error'] = $jsonResultArray;

        return $result;
    }

    /**
     * 删除索引
     * @param string $index 索引名
     * @return array $result 数组信息
     */
    public function deleteIndex(string $index)
    {
        $this->validateIndexName($index);

        $result = ['code' => 10000,'msg' => 'es删除索引失败','status' => 0,'error' => null];

        $url = "{$this->esHost}/{$index}";
        $response = http_delete($url, $this->headers);
        $jsonResultArray = $this->parseResponse($response);

        /**成功
         * Array
            (
                [acknowledged] => 1
            )
         */
        if (isset($jsonResultArray['acknowledged']) && $jsonResultArray['acknowledged']) {
            $result['code'] = 0;
            $result['msg'] = 'es索引删除成功';
            $result['status'] = 1;
        }

        /**
         * 失败=>本来就不存在
         * Array
            (
                [error] => Array
                    (
                        [root_cause] => Array
                            (
                                [0] => Array
                                    (
                                        [type] => index_not_found_exception
                                        [reason] => no such index [youhu_users_index]
                                        [resource.type] => index_or_alias
                                        [resource.id] => youhu_users_index
                                        [index_uuid] => _na_
                                        [index] => youhu_users_index
                                    )

                            )

                        [type] => index_not_found_exception
                        [reason] => no such index [youhu_users_index]
                        [resource.type] => index_or_alias
                        [resource.id] => youhu_users_index
                        [index_uuid] => _na_
                        [index] => youhu_users_index
                    )

                [status] => 404
            )
         */

        if (isset($jsonResultArray['error'])) {
            if (isset($jsonResultArray['status']) && (int)$jsonResultArray['status'] == 404) {
                $result['code'] = 0;
                $result['msg'] = 'es索引删除成功';
                $result['status'] = 1;
            }
        }

        $result['error'] = $jsonResultArray;

        return $result;
    }


    /**
     * 创建文档
     *
     * @param string $index 索引名称
     * @param array $data 文档数据
     * @param string|null $docId 文档ID，为null时自动生成
     * @param bool $refresh 默认开启强制刷新
     * @return array  $result 数组信息
     */
    public function createDoc(string $index, array $data, string $docId = null, bool $refresh = true): array
    {
        $this->validateIndexName($index);

        $result = ['code' => 10000,'msg' => 'es文档创建失败','status' => 0,'error' => null];

        // 拼接URL
        if ($docId) {
            // 指定ID创建（PUT）
            $url = "{$this->esHost}/{$index}/_doc/{$docId}?refresh=true";
            if (!$refresh) {
                $url = "{$this->esHost}/{$index}/_doc/{$docId}";
            }

            $response = http_put($url, $this->headers, $data);
        } else {
            // 自动生成ID（POST）
            $url = "{$this->esHost}/{$index}/_doc?refresh=true";

            if (!$refresh) {
                $url = "{$this->esHost}/{$index}/_doc";
            }

            $response = http_post($url, $this->headers, $data);
        }

        $jsonResultArray =  $this->parseResponse($response);

        /**
         * 创建成功
         * Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286953
                [_version] => 1
                [result] => created
                [_shards] => Array
                    (
                        [total] => 1
                        [successful] => 1
                        [failed] => 0
                    )

                [_seq_no] => 0
                [_primary_term] => 1
            )
         */

        if (isset($jsonResultArray['result']) && $jsonResultArray['result'] == 'created') {
            $result['code'] = 0;
            $result['msg'] = 'es文档创建成功';
            $result['status'] = 1;
        }

        /**
         * 更新成功
         * Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286953
                [_version] => 2
                [result] => updated
                [_shards] => Array
                    (
                        [total] => 1
                        [successful] => 1
                        [failed] => 0
                    )

                [_seq_no] => 1
                [_primary_term] => 1
            )

         */

        if (isset($jsonResultArray['result']) && $jsonResultArray['result'] == 'updated') {
            $result['code'] = 0;
            $result['msg'] = 'es文档更新成功';
            $result['status'] = 2;
        }

        $result['error'] = $jsonResultArray;

        return $result;
    }

    /**
     * 获取单个文档
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @return array 响应结果
     * @throws \Exception
     */
    public function findDoc(string $index, string $docId): array
    {
        $this->validateIndexName($index);

        $result = ['code' => 10000,'msg' => 'es文档查找失败','status' => 0,'error' => null,'data' => []];

        $url = "{$this->esHost}/{$index}/_doc/{$docId}";
        $response = http_get($url, $this->headers);

        $jsonResultArray = $this->parseResponse($response);

        /**
         * 成功
         *
         Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286953
                [_version] => 2
                [_seq_no] => 1
                [_primary_term] => 1
                [found] => 1
                [_source] => Array
                    (
                        [phone] =>
                        [account_name] => develop
                    )

            )
         */

        /**
         * 失败
            Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286954
                [found] =>
            )
         */
        if (isset($jsonResultArray['_source']) && isset($jsonResultArray['found']) && $jsonResultArray['found']) {
            $result['code'] = 0;
            $result['msg'] = 'es文档查找成功';
            $result['status'] = 1;
            $result['data'] = $jsonResultArray['_source'];
        }


        $result['error'] = $jsonResultArray;

        return $result;
    }

    /**
     * 更新文档（全量/局部）
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @param array $data 更新数据（数组）
     * @param bool $isPartial 是否局部更新（true=局部，false=全量）
     * @param bool $refresh 默认开启强制刷新
     * @return array 响应结果
     * @throws \Exception
     */
    public function updateDoc(string $index, string $docId, array $data, bool $isPartial = true, bool $refresh = true): array
    {
        $this->validateIndexName($index);

        $result = ['code' => 10000,'msg' => 'es文档更新失败','status' => 0,'error' => null,'data' => []];

        if ($isPartial) {
            // 局部更新（ES推荐方式）
            $url = "{$this->esHost}/{$index}/_update/{$docId}?refresh=true";

            if (!$refresh) {
                $url = "{$this->esHost}/{$index}/_update/{$docId}";
            }

            $postData = ['doc' => $data];
        } else {
            // 全量替换
            $url = "{$this->esHost}/{$index}/_doc/{$docId}?refresh=true";

            if (!$refresh) {
                $url = "{$this->esHost}/{$index}/_doc/{$docId}";
            }
            $postData = $data;
        }

        $response = http_post($url, $this->headers, $postData);
        $jsonResultArray =  $this->parseResponse($response);

        /**
         * 无需更新
         *Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286953
                [_version] => 2
                [result] => noop
                [_shards] => Array
                    (
                        [total] => 0
                        [successful] => 0
                        [failed] => 0
                    )

                [_seq_no] => 1
                [_primary_term] => 1
            )
         */

        if (isset($jsonResultArray['result']) && $jsonResultArray['result'] == 'noop') {
            $result['code'] = 0;
            $result['msg'] = 'es文档无更新（数据未变化）';
            $result['status'] = 3; // 新增状态码：3=无操作
        }

        /**
         * 更新成功
         * Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286953
                [_version] => 3
                [result] => updated
                [_shards] => Array
                    (
                        [total] => 1
                        [successful] => 1
                        [failed] => 0
                    )

                [_seq_no] => 2
                [_primary_term] => 1
            )
         */

        if (isset($jsonResultArray['result']) && $jsonResultArray['result'] == 'updated') {
            $result['code'] = 0;
            $result['msg'] = 'es文档更新成功';
            $result['status'] = 2;
        }


        if (isset($jsonResultArray['result']) && $jsonResultArray['result'] == 'created') {
            $result['code'] = 0;
            $result['msg'] = 'es文档创建成功';
            $result['status'] = 1;
        }

        /**
         * 失败
         * Array
            (
                [error] => Array
        (
            [root_cause] => Array
                (
                    [0] => Array
                        (
                            [type] => document_missing_exception
                            [reason] => [_doc][276407645251371]: document missing
                            [index_uuid] => 4uFQOc2dTtmHftB7I1nQmQ
                            [shard] => 0
                            [index] => youhu_users_index
                        )

                )

            [type] => document_missing_exception
            [reason] => [_doc][276407645251371]: document missing
            [index_uuid] => 4uFQOc2dTtmHftB7I1nQmQ
            [shard] => 0
            [index] => youhu_users_index
        )

                [status] => 404
            )
         */

        $result['error'] = $jsonResultArray;

        return $result;
    }

    /**
     * 删除单个文档
     * @param string $index 索引名
     * @param string $docId 文档ID
     * @param bool $refresh 默认开启强制刷新
     * @return array 响应结果
     * @throws \Exception
     */
    public function deleteDoc(string $index, string $docId, bool $refresh = true): array
    {
        $this->validateIndexName($index);

        $result = ['code' => 10000,'msg' => 'es文档删除','status' => 0,'error' => null];

        $url = "{$this->esHost}/{$index}/_doc/{$docId}?refresh=true";

        if (!$refresh) {
            $url = "{$this->esHost}/{$index}/_doc/{$docId}";
        }

        $response = http_delete($url, $this->headers);
        $jsonResultArray = $this->parseResponse($response);

        /**
         * 删除成功
         * Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286953
                [_version] => 2
                [result] => deleted
                [_shards] => Array
                    (
                        [total] => 1
                        [successful] => 1
                        [failed] => 0
                    )

                [_seq_no] => 5
                [_primary_term] => 1
            )
         */

        if (isset($jsonResultArray['result']) && $jsonResultArray['result'] == 'deleted') {
            $result['code'] = 0;
            $result['msg'] = 'es文档删除成功';
            $result['status'] = 1; // 新增状态码：3=无操作
        }


        /**
         * 不存在数据
         * Array
            (
                [_index] => youhu_users_index
                [_type] => _doc
                [_id] => 276406781286953
                [_version] => 3
                [result] => not_found
                [_shards] => Array
                    (
                        [total] => 1
                        [successful] => 1
                        [failed] => 0
                    )

                [_seq_no] => 6
                [_primary_term] => 1
            )
         */


        if (isset($jsonResultArray['result']) && $jsonResultArray['result'] == 'not_found') {
            $result['code'] = 0;
            $result['msg'] = 'es文档不存在';
            $result['status'] = 4; // 新增状态码：3=无操作
        }

        $result['error'] = $jsonResultArray;

        return $result;
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
        $this->validateIndexName($index);


        $result = ['code' => 10000,'msg' => 'es文档批量查询','status' => 0,'error' => null,'data' => []];


        $url = "{$this->esHost}/{$index}/_search";
        $postData = [
            'from' => $from,
            'size' => $size,
            'query' => $query
        ];

        $response = http_post($url, $this->headers, $postData);
        $jsonResultArray = $this->parseResponse($response);

        /**
         * 示例:无数据
         * Array
            (
                [took] => 0
                [timed_out] =>
                [_shards] => Array
                    (
                        [total] => 1
                        [successful] => 1
                        [skipped] => 0
                        [failed] => 0
                    )

                [hits] => Array
                    (
                        [total] => Array
                            (
                                [value] => 0
                                [relation] => eq
                            )

                        [max_score] =>
                        [hits] => Array
                            (
                            )

                    )

            )
         * 示例:有数据
         * Array
            (
                [took] => 0
                [timed_out] =>
                [_shards] => Array
                    (
                        [total] => 1
                        [successful] => 1
                        [skipped] => 0
                        [failed] => 0
                    )

                [hits] => Array
                    (
                        [total] => Array
                            (
                                [value] => 1
                                [relation] => eq
                            )

                        [max_score] => 0.2876821
                        [hits] => Array
                            (
                                [0] => Array
                                    (
                                        [_index] => youhu_users_index
                                        [_type] => _doc
                                        [_id] => 276406781286953
                                        [_score] => 0.2876821
                                        [_source] => Array
                                            (
                                                [phone] => 15688523140
                                                [account_name] => develop
                                                [email] =>
                                                [account_status] => 1
                                                [real_auth_status] => 10
                                                [created_at] => 2026-02-12 02:18:20
                                                [id_number] =>
                                                [nick_name] => developer
                                                [real_name] =>
                                                [sex] => 0
                                                [solar_birthday_at] =>
                                                [chinese_birthday_at] =>
                                            )

                                    )

                            )

                    )

            )
            */

        if (isset($jsonResultArray['hits'])) {
            $result['code'] = 0;
            $result['msg'] = 'es文档批量查询';
            $result['status'] = 1;
        }


        $result['data'] = $jsonResultArray;

        return $result;
    }

    /**
     * 按条件删除文档
     * @param string $index 索引名
     * @param array $query 删除条件
     * @param bool $refresh 默认开启强制刷新
     * @return array 响应结果
     * @throws \Exception
     */
    public function deleteByQuery(string $index, array $query, bool $refresh = true): array
    {
        $this->validateIndexName($index);


        $result = ['code' => 10000,'msg' => 'es文档批量删除','status' => 0,'error' => null];


        $url = "{$this->esHost}/{$index}/_delete_by_query?ignore_unavailable=true&conflicts=proceed&refresh=true";

        if (!$refresh) {
            $url = "{$this->esHost}/{$index}/_delete_by_query?ignore_unavailable=true&conflicts=proceed";
        }

        $postData = ['query' => $query];
        $response = http_post($url, $this->headers, $postData);

        $jsonResultArray = $this->parseResponse($response);


        /**
         * 删除成功
         * Array
            (
                [took] => 22
                [timed_out] =>
                [total] => 1
                [deleted] => 1
                [batches] => 1
                [version_conflicts] => 0
                [noops] => 0
                [retries] => Array
                    (
                        [bulk] => 0
                        [search] => 0
                    )

                [throttled_millis] => 0
                [requests_per_second] => -1
                [throttled_until_millis] => 0
                [failures] => Array
                    (
                    )

            )
         */

        /**无数据删除
         * Array
            (
                [took] => 0
                [timed_out] =>
                [total] => 0
                [deleted] => 0
                [batches] => 0
                [version_conflicts] => 0
                [noops] => 0
                [retries] => Array
                    (
                        [bulk] => 0
                        [search] => 0
                    )

                [throttled_millis] => 0
                [requests_per_second] => -1
                [throttled_until_millis] => 0
                [failures] => Array
                    (
                    )

            )
         */

        if (isset($jsonResultArray['total'])) {
            $result['code'] = 0;
            $result['msg'] = 'es文档批量删除成功';
            $result['status'] = 1;
        }

        $result['error'] = $jsonResultArray;

        return $result;
    }

	/**
	 * 清空当前索引内全部文档（保留索引结构、mapping不变）
	 * @param string $index 索引名称
	 * @param bool $refresh 强制刷新
	 * @return array
	 */
	public function clearAllDoc(string $index, bool $refresh = true): array
	{
		$allQuery = [
			'match_all' => new \stdClass()
		];
		return $this->deleteByQuery($index, $allQuery, $refresh);
	}

    /**
     * 批量写入/更新文档（直接调用ES _bulk API）
     *
     * @param string $index 索引名称
     * @param array $data 批量数据（每条含 _docId 字段）
     * @param bool $refresh 默认开启强制刷新
     * @return array 统一格式的操作结果
     */
    public function batchActDoc(string $index, array $data, bool $refresh = true): array
    {
        $this->validateIndexName($index);

        // 统一返回格式（和其他方法保持一致）
        $result = ['code' => 10000,'msg' => 'es批量写入失败','status' => 0,'error' => null];

        // 空数据直接返回成功
        if (empty($data)) {
            $result['code'] = 0;
            $result['msg'] = 'es批量写入成功（无数据）';
            $result['status'] = 1;
            return $result;
        }

        // 拼接bulk数据
        $url = "{$this->esHost}/_bulk?refresh=true";

        if (!$refresh) {
            $url = "{$this->esHost}/_bulk";
        }

        $bulkData = '';
        foreach ($data as $item) {
            $id = $item['_docId'] ?? null;
            unset($item['_docId']);
            $bulkData .= json_encode(['index' => ['_index' => $index, '_id' => $id]]) . "\n";
            $bulkData .= json_encode($item, JSON_UNESCAPED_UNICODE) . "\n";
        }

        // 发送请求并解析
        $response = http_post($url, $this->headers, $bulkData);
        $jsonResultArray = $this->parseResponse($response);

        // 判断是否成功（ES bulk返回errors=false表示全部成功）
        if (!isset($jsonResultArray['errors']) || $jsonResultArray['errors'] === false) {
            $result['code'] = 0;
            $result['msg'] = 'es批量写入成功';
            $result['status'] = 1;
        }
        $result['error'] = $jsonResultArray;

        return $result;
    }

    /**
     * 批量删除文档（支持单ID/多ID/条件删除）
     *
     * @param string $index 索引名称
     * @param string|array $dataOrCondition 删除条件：
     *  - 字符串：单个文档ID
     *  - 数组（一维）：多个文档ID ['1','2','3']
     *  - 数组（二维）：ES查询条件 ['match' => ['title' => '测试']]
     * @param bool $refresh 默认开启强制刷新
     * @return array 统一格式的操作结果
     */
    public function batchDeleteDoc(string $index, string|array $dataOrCondition, bool $refresh = true): array
    {
        $this->validateIndexName($index);

        // 统一返回格式
        $result = ['code' => 10000,'msg' => 'es批量删除失败','status' => 0,'error' => null];

        // 1. 单个文档ID删除
        if (is_scalar($dataOrCondition)) {
            $result = $this->deleteDoc($index, $dataOrCondition);
        }
        // 2. 多个文档ID删除（一维数组）
        elseif (is_array($dataOrCondition) && isset($dataOrCondition[0]) && is_scalar($dataOrCondition[0])) {
            $url = "{$this->esHost}/_bulk?refresh=true";

            if (!$refresh) {
                $url = "{$this->esHost}/_bulk";
            }

            $bulkData = '';
            foreach ($dataOrCondition as $docId) {
                $bulkData .= json_encode(['delete' => ['_index' => $index, '_id' => $docId]]) . "\n";
            }
            $response = http_post($url, $this->headers, $bulkData);
            $jsonResultArray = $this->parseResponse($response);

            if (!isset($jsonResultArray['errors']) || $jsonResultArray['errors'] === false) {
                $result['code'] = 0;
                $result['msg'] = 'es批量删除成功';
                $result['status'] = 1;
            }
            $result['error'] = $jsonResultArray;
        }
        // 3. 条件删除（二维数组）
        elseif (is_array($dataOrCondition) && !isset($dataOrCondition[0])) {
            $result = $this->deleteByQuery($index, $dataOrCondition);
        }

        return $result;
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
            throw new CommonException('ResponseEmptyError');
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CommonException('ResponseConvertError');
        }

        return $result;
    }

    /**
     * 自定义ES请求（兼容特殊操作）
     * @param string $method 请求方法（GET/POST/PUT/DELETE）
     * @param string $path 请求路径（如：/_cat/indices）
     * @param array $data 请求数据（可选）
     * @param bool $refresh 默认开启强制刷新
     * @return array 响应结果
     * @throws \Exception
     */
    public function customRequest(string $method, string $path, array $data = [], bool $refresh = true): array
    {
        $method = strtoupper($method);
        // 先拼接基础路径
        $baseUrl = "{$this->esHost}/" . ltrim($path, '/');

        $isSearch = str_contains($path, '_search');
        $isCount = str_contains($path, '_count');

        if ($refresh && !$isSearch && !$isCount) {
            if (str_contains($baseUrl, '?')) {
                $url = $baseUrl . '&refresh=true';
            } else {
                $url = $baseUrl . '?refresh=true';
            }
        } else {
            $url = $baseUrl;
        }


        switch ($method) {
            case 'GET':
                $response = http_get($url, $this->headers);
                break;
            case 'POST':
                $response = http_post($url, $this->headers, $data);
                break;
            case 'PUT':
                $response = http_put($url, $this->headers, $data);
                break;
            case 'DELETE':
                $response = http_delete($url, $this->headers, $data);
                break;
            default:
                throw new CommonException('CustomRequestMethodError');
        }

        return $this->parseResponse($response);
    }

    /**
     * 验证 Elasticsearch 索引名称是否符合规范
     *
     * 索引名称规则：
     * - 必须小写
     * - 只能包含字母、数字、下划线、短横线
     * - 不能以短横线开头
     *
     * @param string $index 待验证的索引名称
     * @throws CommonException 当索引名称不符合规范时抛出异常
     * @return void
     */
    private function validateIndexName(string $index): void
    {
        // ES索引名规范：小写、只能包含字母/数字/下划线/短横线，不能以-开头
        $result = preg_match('/^[a-z0-9_][a-z0-9_\-]*$/', $index) === 1;

        if (!$result) {
            throw new CommonException("EsIndexError");
        }
    }
}

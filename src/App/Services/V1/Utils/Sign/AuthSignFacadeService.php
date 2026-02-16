<?php
/*
 * @Descripttion: 认证签名服务类
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-17 02:49:09
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-17 02:49:09
 * @FilePath: App\Services\V1\Utils\Sign\AuthSignFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Utils\Sign;

/**
 * 认证签名服务类
 *
 * 提供HMAC-SHA256签名生成等认证相关功能
 */
class AuthSignFacadeService
{
    /**
     * 生成HMAC-SHA256签名
     *
     * @param array $params 待签名的参数数组
     * @param string $secretKey 密钥
     * @return string 生成的签名
     */
    public function makeSign($params, $secretKey): string
    {
        // 对参数按键名排序
        ksort($params);

        // 构建查询字符串
        $queryString = http_build_query($params, '', '&');

        // 拼接最终字符串并添加密钥
        $finalString = urldecode($queryString) . '&secretKey=' . $secretKey;

        // 使用HMAC-SHA256生成签名
        $sign = hash_hmac('sha256', $finalString, $secretKey);

        return $sign;
    }
}

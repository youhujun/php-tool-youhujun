<?php

// HTTP请求相关
if (!function_exists('httpGet')) {
    /**
     * CURL GET请求
     *
     * @param string $url 请求地址
     * @return string|false 请求结果（失败返回false）
     * @throws Exception 请求超时/失败抛出异常
     */
    function httpGet($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('无效的URL地址');
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true, // 跟随重定向
        ]);

        $res = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new Exception("GET请求失败：{$error}");
        }

        return $res;
    }
}

if (!function_exists('httpPost')) {
    /**
     * CURL POST请求
     *
     * @param string $url 请求地址
     * @param array $headers 请求头（可选）
     * @param mixed $data POST数据（可选）
     * @return string|false 请求结果（失败返回false）
     * @throws Exception 请求异常抛出
     */
    function httpPost($url, $headers = [], $data = null)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('无效的URL地址');
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 10,
        ]);

        // 设置请求头
        if (!empty($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // 设置POST数据
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            // 如果是数组且包含json头，自动转JSON
            if (is_array($data) && in_array('Content-Type:application/json', $headers)) {
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("POST请求失败：{$error}");
        }

        return $res;
    }
}

/**
 * CURL PUT 请求（适配 ES 更新操作）
 *
 * @param string $url 请求地址（如 ES 的文档更新接口）
 * @param array $headers 请求头数组（默认空）
 * @param mixed $data 提交的数据（数组/JSON 字符串）
 * @return string|false 请求结果（失败返回false）
 * @throws Exception 请求异常抛出
 */
if (!function_exists('httpPut')) {
    function httpPut($url, $headers = [], $data = null)
    {
        // URL 合法性校验（和 POST/GET 保持一致）
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('无效的URL地址');
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 10,
            // PUT 请求核心配置
            CURLOPT_CUSTOMREQUEST => 'PUT', // 指定 PUT 方法
        ]);

        // 设置请求头
        if (!empty($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // 处理请求数据（自动适配 JSON，和 POST 逻辑一致）
        if (!empty($data)) {
            // 如果是数组且包含 JSON 头，自动转 JSON 字符串
            if (is_array($data) && in_array('Content-Type:application/json', $headers)) {
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        // 执行请求并捕获错误
        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("PUT请求失败：{$error}");
        }

        return $res;
    }
}

/**
 * CURL DELETE 请求（适配 ES 删除操作）
 *
 * @param string $url 请求地址（如 ES 的文档删除接口）
 * @param array $headers 请求头数组（默认空）
 * @param mixed $data 提交的数据（可选，部分 ES 删除接口需要传参）
 * @return string|false 请求结果（失败返回false）
 * @throws Exception 请求异常抛出
 */
if (!function_exists('httpDelete')) {
    function httpDelete($url, $headers = [], $data = null)
    {
        // URL 合法性校验
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('无效的URL地址');
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 10,
            // DELETE 请求核心配置
            CURLOPT_CUSTOMREQUEST => 'DELETE', // 指定 DELETE 方法
        ]);

        // 设置请求头
        if (!empty($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // 部分 ES 删除接口需要传数据（如按条件删除）
        if (!empty($data)) {
            if (is_array($data) && in_array('Content-Type:application/json', $headers)) {
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        // 执行请求并捕获错误
        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("DELETE请求失败：{$error}");
        }

        return $res;
    }
}
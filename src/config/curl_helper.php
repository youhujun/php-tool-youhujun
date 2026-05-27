<?php


// HTTP请求相关
if (!function_exists('http_get')) {
    /**
     * CURL GET请求
     *
     * @param string $url 请求地址
     * @param array $headers 请求头（默认空数组）
     * @return string|false 请求结果（失败返回false）
     * @throws Exception 请求超时/失败抛出异常
     */
    function http_get($url, $headers = [])
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
            CURLOPT_HTTPGET => true, // 显式声明GET请求
        ]);

        // 设置请求头（修复$ch错误 + 容错处理）
        $headers = is_array($headers) ? $headers : [];
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        $res = curl_exec($curl);
        $error = curl_error($curl);
        $errno = curl_errno($curl);
        curl_close($curl);

        // 严格判断请求是否失败
        if ($res === false) {
            throw new Exception("GET请求执行失败：{$error}（错误码：{$errno}）");
        }
        if ($errno !== 0) {
            throw new Exception("GET请求异常：{$error}（错误码：{$errno}）");
        }

        return $res;
    }
}


if (!function_exists('http_post')) {
    function http_post($url, $headers = [], $data = null)
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
            CURLOPT_TIMEOUT => 30,          // 改成30秒，不容易超时
            CURLOPT_USERAGENT => 'PHP-curl',// 加UA，避免被拦截
        ]);

        // 设置请求头
        if (!empty($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // 设置POST数据
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (is_array($data)) {
                // 如果headers里有json，才转json；否则用form格式
                $hasJson = in_array('Content-Type:application/json', $headers);
                if ($hasJson) {
                    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                } else {
                    $data = http_build_query($data);
                }
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
if (!function_exists('http_put')) {
    function http_put($url, $headers = [], $data = null)
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
if (!function_exists('http_delete')) {
    function http_delete($url, $headers = [], $data = null)
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


    /**
     * CURL HEAD 请求（适配 ES 索引存在性检查等场景）
     *
     * @param string $url 请求地址（如 ES 的索引检查接口）
     * @param array $headers 请求头数组（默认空）
     * @return string 请求结果（返回完整响应头，失败抛异常）
     * @throws Exception 请求异常/无效URL抛出
     */
    if (!function_exists('http_head')) {
        function http_head($url, $headers = [])
        {
            // 1. URL 合法性校验（和其他HTTP函数保持一致）
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('无效的URL地址');
            }

            // 2. 初始化CURL并设置核心参数
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HEADER => 1, // 关键：返回响应头（HEAD请求核心需求）
                CURLOPT_RETURNTRANSFER => 1, // 返回结果而非直接输出
                CURLOPT_SSL_VERIFYPEER => false, // 兼容HTTPS（测试/生产按需调整）
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 10, // 超时时间（和其他函数一致）
                CURLOPT_FOLLOWLOCATION => true, // 跟随重定向（适配ES可能的重定向）
                CURLOPT_NOBODY => true, // 关键：只返回头，不返回响应体（HEAD请求特性）
                CURLOPT_CUSTOMREQUEST => 'HEAD', // 显式声明HEAD请求方法
            ]);

            // 3. 设置请求头（容错+和其他函数逻辑一致）
            $headers = is_array($headers) ? $headers : [];
            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            // 4. 执行请求并捕获错误
            $res = curl_exec($ch);
            $error = curl_error($ch);
            $errno = curl_errno($ch); // 补充错误码，和httpGet保持一致的容错粒度
            curl_close($ch);

            // 5. 严格的错误判断（和httpGet保持一致）
            if ($res === false) {
                throw new Exception("HEAD请求执行失败：{$error}（错误码：{$errno}）");
            }
            if ($errno !== 0) {
                throw new Exception("HEAD请求异常：{$error}（错误码：{$errno}）");
            }

            return $res;
        }
    }
}

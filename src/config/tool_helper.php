<?php

/*
 * @Descripttion: 自定义助手函数
 * @Author: YouHuJun
 * @Date: 2020-02-20 11:25:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-07-12 20:33:23
 */

// 格式化打印函数
if (!function_exists('p')) {
    /**
     * 打印函数（格式化输出，便于调试）
     *
     * @param mixed $param 要打印的参数，可以是任意类型
     * @return void
     */
    function p($param): void
    {
        echo "<pre>";
        print_r($param);
        echo "</pre>";
    }
}

// 过滤HTML标签/转义实体
if (!function_exists('f')) {
    /**
     * 过滤字符串中的HTML标签/转义HTML实体
     *
     * @param mixed $param 输入的字符串或数组
     * @param int $type 过滤类型：0=转义实体（默认），1=去除标签
     * @return mixed 过滤后的字符串/数组
     */
    function f($param, $type = 0)
    {
        // 数组递归处理（修复：原代码未赋值回数组）
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = f($value, $type);
            }
            return $param;
        }

        // 非数组处理（简化逻辑）
        if ($type === 1) {
            return strip_tags((string)$param);
        }

        return htmlspecialchars((string)$param, ENT_QUOTES, 'UTF-8');
    }
}

if(!function_exists('maskString')){
	 #[DocParams('脱敏字符串', ['dataString' => ['type' => 'string', 'note' => '待处理的字符串'],
	 'prefixLen' => ['type' => 'int', 'note' => '前缀保留长度'], 'suffixLen' => ['type' => 'int', 'note' => '后缀保留长度'], 'return' => ['type' => 'string', 'note' => '脱敏后的字符串']])]
	function maskString(string $dataString, int $prefixLen = 3, int $suffixLen = 4): string
	{
		// 指定UTF-8编码，按字符计算长度
        $strLen = mb_strlen($dataString, 'UTF-8');
        if ($strLen <= $prefixLen + $suffixLen) {
            return $dataString;
        }
        $prefix = mb_substr($dataString, 0, $prefixLen, 'UTF-8');
        $suffix = mb_substr($dataString, -$suffixLen, $suffixLen, 'UTF-8');
        $star = str_repeat('*', $strLen - $prefixLen - $suffixLen);
        
        return $prefix . $star . $suffix;
	
	}

}


// 接口返回码合并
if (!function_exists('code')) {
    /**
     * 合并接口返回码和附加数据
     *
     * @param array|null $code 配置的返回码数组
     * @param array|null $add 附加数据数组
     * @return array 合并后的结果
     */
    function code($code = [], $add = [])
    {
        // 简化逻辑（原逻辑冗余）
        $code = $code ?? [];
        $add = $add ?? [];

        return array_merge($code, $add);
    }
}

// 检测序列化字符串
if (!function_exists('is_serialized')) {
    /**
     * 检测字符串是否为PHP序列化格式
     *
     * @param mixed $data 待检测数据
     * @return bool
     */
    function is_serialized($data)
    {
        // 非字符串直接返回false
        if (!is_string($data)) {
            return false;
        }

        $data = trim($data);

        // 空序列化
        if ($data === 'N;') {
            return true;
        }

        // 匹配序列化开头标识
        if (!preg_match('/^([adObis]):/', $data, $matches)) {
            return false;
        }

        $type = $matches[1];
        switch ($type) {
            case 'a':
            case 'O':
            case 's':
                return preg_match("/^{$type}:[0-9]+:.*[;}]\$/s", $data);
            case 'b':
            case 'i':
            case 'd':
                return preg_match("/^{$type}:[0-9.E-]+;\$/", $data);
            default:
                return false;
        }
    }
}










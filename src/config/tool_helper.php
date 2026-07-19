<?php

/*
 * @Descripttion: 自定义助手函数
 * @Author: YouHuJun
 * @Date: 2020-02-20 11:25:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-07-19 15:24:37
 */
use YouHuJun\Tool\App\Annotations\DocParams;

if (!function_exists('p')) {
    
    #[DocParams('格式化打印变量，便于调试', ['param' => ['type' => 'mixed', 'note' => '要打印的参数，可以是任意类型'], 'return' => ['type' => 'void', 'note' => '无返回值']])]
    function p(mixed $param): void
    {
        echo "<pre>";
        print_r($param);
        echo "</pre>";
    }
}

// 过滤HTML标签/转义实体
if (!function_exists('f')) {
   
    #[DocParams('过滤HTML标签或转义HTML实体，支持数组递归处理', ['param' => ['type' => 'mixed', 'note' => '输入的字符串或数组'], 'type' => ['type' => 'int', 'note' => '过滤类型：0=转义实体(默认)，1=去除标签'], 'return' => ['type' => 'mixed', 'note' => '过滤后的数据']])]
    function f(mixed $param, int $type = 0): mixed
    {
        if (is_numeric($param)) {
            return $param;
        }
        // 数组递归处理（修复：原代码未赋值回数组）
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = f($value, $type);
            }
            return $param;
        }

        if (is_string($param)) {
            // 非数组处理（简化逻辑）
            if ($type === 1) {
                return strip_tags((string)$param);
            }

            return htmlspecialchars((string)$param, ENT_QUOTES, 'UTF-8');
        }

		return $param;
    }
}

if(!function_exists('mask_string')){
	 #[DocParams('脱敏字符串', ['dataString' => ['type' => 'string', 'note' => '待处理的字符串'],
	 'prefixLen' => ['type' => 'int', 'note' => '前缀保留长度'], 'suffixLen' => ['type' => 'int', 'note' => '后缀保留长度'], 'return' => ['type' => 'string', 'note' => '脱敏后的字符串']])]
	function mask_string(string $dataString, int $prefixLen = 3, int $suffixLen = 4): string
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

if (!function_exists('get_now_date_time')) {
	#[DocParams('获取当前时间', ['return' => ['type' => 'string', 'note' => '当前时间']])]
	function get_now_date_time(): string
	{
		return date('Y-m-d H:i:s');
	}
}

if (!function_exists('get_show_amount')) {
	#[DocParams('获取显示金额', ['amount' => ['type' => 'int', 'note' => '待处理的金额'],'return' => ['type' => 'string', 'note' => '当前时间']])]
	function get_show_amount(int $amount): string
	{
		return bcdiv((string)$amount,'100', 2);
	}
}

// 接口返回码合并
if (!function_exists('code')) {
   
    #[DocParams('合并接口返回码和附加数据', ['code' => ['type' => 'array|null', 'note' => '配置的返回码数组'], 'add' => ['type' => 'array|null', 'note' => '附加数据数组'], 'return' => ['type' => 'array', 'note' => '合并后的结果数组']])]
    function code(?array $code = null, ?array $add = null)
    {
        // 简化逻辑（原逻辑冗余）
        $code = $code ?? [];
        $add = $add ?? [];

        return array_merge($code, $add);
    }
}


// 检测序列化字符串
if (!function_exists('is_serialized')) {
    
    #[DocParams('检测字符串是否为PHP序列化格式', ['data' => ['type' => 'mixed', 'note' => '待检测数据'], 'return' => ['type' => 'bool', 'note' => '是否为序列化字符串']])]
    function is_serialized(mixed $data):bool
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









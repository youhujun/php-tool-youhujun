<?php
/*
 * @Descripttion: 自定义助手函数
 * @Author: YouHuJun
 * @Date: 2020-02-20 11:25:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-15 14:30:05
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



// 数组处理相关
if (!function_exists('array_level')) {
    /**
     * 计算数组维度
     *
     * @param array $arr 待计算数组
     * @return int 数组最大维度
     */
    function array_level(array $arr): int
    {
        $levels = [0];
        total($arr, $levels);
        return max($levels);
    }
}

if (!function_exists('total')) {
    /**
     * 递归计算数组维度（辅助函数）
     *
     * @param mixed $arr 待检测数据
     * @param array &$levels 存储维度的引用数组
     * @param int $level 当前层级
     * @return void
     */
    function total($arr, &$levels, $level = 0): void
    {
        if (is_array($arr)) {
            $level++;
            $levels[] = $level;
            
            foreach ($arr as $v) {
                total($v, $levels, $level);
            }
        }
    }
}

if (!function_exists('toArray')) {
    /**
     * 将数组每个元素转为单元素数组
     *
     * @param mixed $array 输入数据（非数组直接返回）
     * @return array 处理后的数组
     */
    function toArray($array): array
    {
        if (!is_array($array)) {
            return []; // 非数组返回空数组，避免报错
        }

        foreach ($array as $k => &$v) {
            $v = [$v];
        }
        unset($v); // 释放引用，避免后续变量污染

        return $array;
    }
}

// ID合法性检查
if (!function_exists('checkId')) {
    /**
     * 检查ID是否为纯数字
     *
     * @param mixed $id 待检查ID
     * @return int 有效ID返回自身，无效返回0
     */
    function checkId($id): int
    {
        // 简化正则 + 强制转整型，更严谨
        return preg_match('/^\d+$/', (string)$id) ? (int)$id : 0;
    }
}
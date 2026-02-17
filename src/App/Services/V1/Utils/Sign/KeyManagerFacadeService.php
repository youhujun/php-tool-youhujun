<?php
/*
 * @Descripttion: 密钥管理服务类
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-17 02:36:42
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-17 02:36:42
 * @FilePath: App\Services\V1\Utils\Sign\KeyManagerFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Services\V1\Utils\Sign;

/**
 * 密钥管理服务类
 *
 * 提供安全密钥生成等密钥管理功能
 */
class KeyManagerFacadeService
{
    /**
     * 生成安全的随机密钥
     *
     * @param int $length 密钥长度，默认32位
     * @param array $charTypes 字符类型数组，可选值: 'letters_upper', 'letters_lower', 'numbers', 'symbols'
     * @return string 生成的随机密钥
     * @throws \InvalidArgumentException 当字符类型配置错误时抛出异常
     */
    public function generateSecureSecretKey(int $length = 32, array $charTypes = []): string
    {
        // 1. 定义字符池（按类型分类）
        $charPools = [
            'letters_upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'letters_lower' => 'abcdefghijklmnopqrstuvwxyz',
            'numbers'       => '0123456789',
            'symbols'       => '!@#$%^&*()_+-=[]{}|;:,.?~`'
        ];

        // 2. 若未指定字符类型，默认使用全部类型
        if (empty($charTypes)) {
            $charTypes = array_keys($charPools);
        }

        // 3. 拼接最终字符池
        $finalCharPool = '';
        foreach ($charTypes as $type) {
            if (isset($charPools[$type])) {
                $finalCharPool .= $charPools[$type];
            }
        }

        // 4. 校验字符池是否为空（避免无效生成）
        if (empty($finalCharPool)) {
            throw new \InvalidArgumentException('字符类型配置错误，无法生成有效字符池');
        }

        $poolLength = strlen($finalCharPool);
        $secretKey = '';

        // 5. 用密码学安全的随机数生成器生成密钥
        // random_bytes生成二进制随机数，转换后取字符池中的字符
        $randomBytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            // 取每个字节的十进制值，对字符池长度取模，保证索引有效
            $randomIndex = ord($randomBytes[$i]) % $poolLength;
            $secretKey .= $finalCharPool[$randomIndex];
        }

        return $secretKey;
    }
}

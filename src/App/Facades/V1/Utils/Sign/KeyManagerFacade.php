<?php
/*
 * @Descripttion: 密钥管理服务静态门面
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-17 02:36:42
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-17 02:36:42
 * @FilePath: App.Facade.V1.Utils.Sign.KeyManagerFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Facades\V1\Utils\Sign;

use YouHuJun\Tool\App\Services\V1\Utils\Sign\KeyManagerFacadeService;

/**
 * 密钥管理服务静态门面类
 *
 * 提供静态方法调用密钥管理服务
 */
class KeyManagerFacade
{
    /**
     * 服务实例(单例)
     *
     * @var KeyManagerFacadeService|null
     */
    private static ?KeyManagerFacadeService $instance = null;

    /**
     * 获取服务实例
     *
     * @return KeyManagerFacadeService
     */
    private static function getInstance(): KeyManagerFacadeService
    {
        if (self::$instance === null) {
            self::$instance = new KeyManagerFacadeService();
        }

        return self::$instance;
    }

    /**
     * 生成安全的随机密钥
     *
     * @param int $length 密钥长度，默认32位
     * @param array $charTypes 字符类型数组，可选值: 'letters_upper', 'letters_lower', 'numbers', 'symbols'
     * @return string 生成的随机密钥
     * @throws \InvalidArgumentException 当字符类型配置错误时抛出异常
     */
    public static function generateSecureSecretKey(int $length = 32, array $charTypes = []): string
    {
        return self::getInstance()->generateSecureSecretKey($length, $charTypes);
    }
}

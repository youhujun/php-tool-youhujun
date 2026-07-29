<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-01-08 11:33:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-07-30 04:06:14
 * @FilePath: \youhu-laravel-api-13d:\wwwroot\PHP\Components\Tool\youhujun\php-tool-youhujun\src\App\Facades\V1\Qrcode\QrcodeFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */
namespace YouHuJun\Tool\App\Facades\V1\Qrcode;

use YouHuJun\Tool\App\Services\V1\Qrcode\QrcodeFacadeService;
use BadMethodCallException;

use YouHuJun\Tool\App\Attributes\Common\DocNote;
use YouHuJun\Tool\App\Attributes\Common\DocParams;

/**
 * 二维码门面类
 *
 * @see \YouHuJun\Tool\App\Services\V1\Qrcode\QrcodeFacadeService
 */
#[DocNote('二维码门面类')]
class QrcodeFacade 
{
	 /**
     * 单例实例
     * @var QrcodeFacadeService|null
     */
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于依赖注入或测试)
     *
     * @param QrcodeFacadeService $instance 服务实例
     * @return void
     */
    public static function setInstance(QrcodeFacadeService $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * 清除服务实例
     *
     * @return void
     */
    public static function clearInstance(): void
    {
        static::$instance = null;
    }

    /**
     * 获取服务实例
     *
     * @return QrcodeFacadeService 服务实例
     */
    protected static function getInstance(): QrcodeFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new QrcodeFacadeService();
        }
        return static::$instance;
    }

   #[DocParams(' 生成二维码', [
		'config' => ['type' => 'array', 'note' => '配置参数'], 
		'params' => ['type' => 'array', 'note' => '二维码参数'], 
		'mode' => ['type' => 'int', 'note' => '输出模式: 1-保存到文件, 2-直接输出, 3-生成Data URI'], 
		'return' => ['type' => 'mixed','note' => '根据mode不同返回不同结果']])]
    public static function makeQrcode(array $config, array $params, int $mode = 1)
    {
        return static::getInstance()->makeQrcode($config, $params, $mode);
    }

    /**
     * 魔术方法调用(兼容未显式声明的方法)
     *
     * @param string $method 方法名
     * @param array $parameters 参数数组
     * @return mixed
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $instance = static::getInstance();
        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($instance), $method)
            );
        }
        return $instance->$method(...$parameters);
    }
}

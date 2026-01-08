<?php
namespace YouHuJun\Tool\App\Facade\V1\Qrcode;

use YouHuJun\Tool\App\Service\V1\Qrcode\QrcodeFacadeService;
use BadMethodCallException;

/**
 * 二维码门面类
 *
 * @see \YouHuJun\Tool\App\Service\V1\Qrcode\QrcodeFacadeService
 */
class QrcodeFacade 
{
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

    /**
     * 生成二维码
     *
     * @param array $config 配置参数
     * @param array $params 二维码参数
     * @param int $mode 输出模式: 1-保存到文件, 2-直接输出, 3-生成Data URI
     * @return mixed 根据mode不同返回不同结果
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
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

<?php
namespace YouHuJun\Tool\App\Facade\V1\Wechat\Pay\JSAPI;

use YouHuJun\Tool\App\Service\V1\Wechat\Pay\JSAPI\WechatPayDecryptFacadeService;
use BadMethodCallException;

/**
 * 微信JSAPI支付回调解密门面类
 *
 * @see \YouHuJun\Tool\App\Service\V1\Wechat\Pay\JSAPI\WechatPayDecryptFacadeService
 */
class WechatPayDecryptFacade
{
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于依赖注入或测试)
     *
     * @param WechatPayDecryptFacadeService $instance 服务实例
     * @return void
     */
    public static function setInstance(WechatPayDecryptFacadeService $instance): void
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
     * @return WechatPayDecryptFacadeService 服务实例
     */
    protected static function getInstance(): WechatPayDecryptFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new WechatPayDecryptFacadeService();
        }
        return static::$instance;
    }

    /**
     * 验证签名并解密微信支付回调数据
     *
     * @param array $config 支付配置,包含apiv3Key和wechatpayCertificatePath
     * @param array $notifyData 回调数据,包含wechatpay_signature、wechatpay_timestamp、wechatpay_serial、wechatpay_nonce、body
     * @return array 解密后的数据
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function decryptData(array $config, array $notifyData): array
    {
        return static::getInstance()->decryptData($config, $notifyData);
    }

    /**
     * 设置时间偏移量(秒)
     *
     * @param int $seconds 时间偏移量
     * @return void
     */
    public static function setTimeOffset(int $seconds): void
    {
        static::getInstance()->setTimeOffset($seconds);
    }

    /**
     * 获取时间偏移量(秒)
     *
     * @return int 时间偏移量
     */
    public static function getTimeOffset(): int
    {
        return static::getInstance()->getTimeOffset();
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

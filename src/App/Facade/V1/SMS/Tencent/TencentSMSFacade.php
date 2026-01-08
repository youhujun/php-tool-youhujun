<?php
namespace YouHuJun\Tool\App\Facade\V1\SMS\Tencent;

use YouHuJun\Tool\App\Service\V1\SMS\Tencent\TencentSMSFacadeService;
use BadMethodCallException;

/**
 * 腾讯云短信门面类
 *
 * @see \YouHuJun\Tool\App\Service\V1\SMS\Tencent\TencentSMSFacadeService
 */
class TencentSMSFacade 
{
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于依赖注入或测试)
     *
     * @param TencentSMSFacadeService $instance 服务实例
     * @return void
     */
    public static function setInstance(TencentSMSFacadeService $instance): void
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
     * @return TencentSMSFacadeService 服务实例
     */
    protected static function getInstance(): TencentSMSFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new TencentSMSFacadeService();
        }
        return static::$instance;
    }

    /**
     * 发送短信验证码
     *
     * @param array $config 腾讯云配置,包含 secretId, secretKey 等参数
     * @param array $smsParam 短信参数,包含 smsContent, phoneNumber, smsSdkAppId, signName, templateId 等
     * @return int 返回发送结果: 1-成功, 0-失败
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function sendSms(array $config, array $smsParam): int
    {
        return static::getInstance()->sendSms($config, $smsParam);
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

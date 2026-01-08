<?php
namespace YouHuJun\Tool\App\Facade\V1\Wechat\MiniProgram;

use YouHuJun\Tool\App\Service\V1\Wechat\MiniProgram\WechatMiniProgramFacadeService;
use BadMethodCallException;

/**
 * 微信小程序门面类
 *
 * @see \YouHuJun\Tool\App\Service\V1\Wechat\MiniProgram\WechatMiniProgramFacadeService
 */
class WechatMiniProgramFacade
{
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于依赖注入或测试)
     *
     * @param WechatMiniProgramFacadeService $instance 服务实例
     * @return void
     */
    public static function setInstance(WechatMiniProgramFacadeService $instance): void
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
     * @return WechatMiniProgramFacadeService 服务实例
     */
    protected static function getInstance(): WechatMiniProgramFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new WechatMiniProgramFacadeService();
        }
        return static::$instance;
    }

    /**
     * 通过code获取微信小程序用户的openid和session_key
     *
     * @param array $params 参数,包含 code 和 appid
     * @param array $config 配置,包含 appsecret
     * @return array 返回包含openid、session_key等信息的数组
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getOpenIdByCode(array $params, array $config): array
    {
        return static::getInstance()->getOpenIdByCode($params, $config);
    }

    /**
     * 通过code获取微信小程序用户的openid和session_key(返回集合对象格式)
     *
     * @param array $params 参数,包含 code 和 appid
     * @param array $config 配置,包含 appsecret
     * @return object 返回集合对象
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getOpenIdByCodeWithCollection(array $params, array $config): object
    {
        return static::getInstance()->getOpenIdByCodeWithCollection($params, $config);
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

<?php
namespace YouHuJun\Tool\App\Facade\V1\Wechat\Official;

use YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService;
use BadMethodCallException;
use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * 微信公众号网页授权门面类
 *
 * 提供静态方法访问 WechatOfficialWebAuthFacadeService 服务
 * 所有方法都有完整的PHPDoc注释,支持IDE代码提示和自动补全
 *
 * @see \YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService
 */
class WechatOfficialWebAuthFacade
{
    /**
     * 单例实例
     * @var WechatOfficialWebAuthFacadeService|null
     */
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于测试)
     *
     * @param WechatOfficialWebAuthFacadeService $instance
     * @return void
     */
    public static function setInstance(WechatOfficialWebAuthFacadeService $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * 清除单例实例(用于测试)
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
     * @return WechatOfficialWebAuthFacadeService
     */
    protected static function getInstance(): WechatOfficialWebAuthFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new WechatOfficialWebAuthFacadeService();
        }
        return static::$instance;
    }

    /**
     * 获取微信授权码URL
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param int $scopeType 授权范围类型: 10=静默授权, 20=主动授权
     * @param string $authRedirectUrl 授权后重定向的URL
     * @param string $state 用于保持请求和回调的状态
     * @return string 授权URL
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService::getAuthUrl()
     */
    public static function getAuthUrl(array $config, int $scopeType, string $authRedirectUrl, string $state = ''): string
    {
        return static::getInstance()->getAuthUrl($config, $scopeType, $authRedirectUrl, $state);
    }

    /**
     * 通过授权码获取访问令牌
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param string $code 微信授权码
     * @return array 包含access_token, openid等信息的数组
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService::getAccessToken()
     */
    public static function getAccessToken(array $config, string $code): array
    {
        return static::getInstance()->getAccessToken($config, $code);
    }

    /**
     * 获取微信用户信息
     *
     * @param string $accessToken 访问令牌
     * @param string $openid 用户的唯一标识
     * @param string $lang 语言,默认为 zh_CN
     * @return array 用户信息数组
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService::getUserInfo()
     */
    public static function getUserInfo(string $accessToken, string $openid, string $lang = 'zh_CN'): array
    {
        return static::getInstance()->getUserInfo($accessToken, $openid, $lang);
    }

    /**
     * 完整的授权流程:获取访问令牌和用户信息
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param string $code 微信授权码
     * @param bool $needUserInfo 是否获取用户信息
     * @return array 包含access_token, openid, userinfo(可选)的数组
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService::authorize()
     */
    public static function authorize(array $config, string $code, bool $needUserInfo = true): array
    {
        return static::getInstance()->authorize($config, $code, $needUserInfo);
    }

    /**
     * 刷新访问令牌
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param string $refreshToken 刷新令牌
     * @return array 包含新的access_token信息的数组
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService::refreshAccessToken()
     */
    public static function refreshAccessToken(array $config, string $refreshToken): array
    {
        return static::getInstance()->refreshAccessToken($config, $refreshToken);
    }

    /**
     * 检查access_token是否有效
     *
     * @param string $accessToken 访问令牌
     * @param string $openid 用户的唯一标识
     * @return bool
     *
     * @see \YouHuJun\Tool\App\Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService::checkAccessToken()
     */
    public static function checkAccessToken(string $accessToken, string $openid): bool
    {
        return static::getInstance()->checkAccessToken($accessToken, $openid);
    }

    /**
     * 动态调用未在Facade中显式声明的方法
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

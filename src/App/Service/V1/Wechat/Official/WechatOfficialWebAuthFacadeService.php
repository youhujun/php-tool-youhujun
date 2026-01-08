<?php
/*
 * @Descripttion: 微信公众号网页授权服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-06 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-06 00:01:25
 * @FilePath: App.Service\V1\Wechat\Official\WechatOfficialWebAuthFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Wechat\Official;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * 微信公众号网页授权服务类
 *
 * 通过方法传递微信配置,由外部控制配置的来源
 */
class WechatOfficialWebAuthFacadeService
{
    // 10 静默授权 20 主动授权
    protected $scope_array = [10 => 'snsapi_base', 20 => 'snsapi_userinfo'];

    /**
     * 获取微信授权码URL
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param int $scopeType 授权范围类型: 10=静默授权, 20=主动授权
     * @param string $authRedirectUrl 授权后重定向的URL
     * @param string $state 用于保持请求和回调的状态
     * @return string 授权URL
     * @throws CommonException
     */
    public function getAuthUrl(array $config, int $scopeType, string $authRedirectUrl, string $state = ''): string
    {
        $this->validateConfig($config);

        if (!isset($this->scope_array[$scopeType])) {
            throw new CommonException('WechatInvalidScopeType');
        }

        $appid = trim($config['appid']);
        $scope = $this->scope_array[$scopeType];
        $redirectUrl = urlencode($authRedirectUrl);

        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirectUrl}&response_type=code&scope={$scope}&state={$state}&connect_redirect=1#wechat_redirect";
    }

    /**
     * 通过授权码获取访问令牌
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param string $code 微信授权码
     * @return array 包含access_token, openid等信息的数组
     * @throws CommonException
     */
    public function getAccessToken(array $config, string $code): array
    {
        $this->validateConfig($config);

        $appid = trim($config['appid']);
        $appsecret = trim($config['appsecret']);

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";

        $response = httpGet($url);
        $result = json_decode($response, true) ?: [];

        // 检查是否有错误
        if (isset($result['errcode'])) {
            throw new CommonException('WechatOfficialGetAccessTokenError');
        }

        return $result;
    }

    /**
     * 获取微信用户信息
     *
     * @param string $accessToken 访问令牌
     * @param string $openid 用户的唯一标识
     * @param string $lang 语言,默认为 zh_CN
     * @return array 用户信息数组
     * @throws CommonException
     */
    public function getUserInfo(string $accessToken, string $openid, string $lang = 'zh_CN'): array
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$accessToken}&openid={$openid}&lang={$lang}";

        $response = httpGet($url);
        $result = json_decode($response, true) ?: [];

        if (!isset($result['openid'])) {
            throw new CommonException('WechatOfficialUserInfoError');
        }

        return $result;
    }

    /**
     * 完整的授权流程:获取访问令牌和用户信息
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param string $code 微信授权码
     * @param bool $needUserInfo 是否获取用户信息
     * @return array 包含access_token, openid, userinfo(可选)的数组
     * @throws CommonException
     */
    public function authorize(array $config, string $code, bool $needUserInfo = true): array
    {
        $result = $this->getAccessToken($config, $code);

        if ($needUserInfo && isset($result['scope']) && strpos($result['scope'], 'snsapi_userinfo') !== false) {
            $userInfo = $this->getUserInfo($result['access_token'], $result['openid']);
            $result['userinfo'] = $userInfo;
        }

        return $result;
    }

    /**
     * 刷新访问令牌
     *
     * @param array $config 微信配置,必须包含 appid 和 appsecret
     * @param string $refreshToken 刷新令牌
     * @return array 包含新的access_token信息的数组
     * @throws CommonException
     */
    public function refreshAccessToken(array $config, string $refreshToken): array
    {
        $this->validateConfig($config);

        $appid = trim($config['appid']);

        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$appid}&grant_type=refresh_token&refresh_token={$refreshToken}";

        $response = httpGet($url);
        $result = json_decode($response, true) ?: [];

        if (isset($result['errcode'])) {
            throw new CommonException('WechatOfficialRefreshTokenError');
        }

        return $result;
    }

    /**
     * 检查access_token是否有效
     *
     * @param string $accessToken 访问令牌
     * @param string $openid 用户的唯一标识
     * @return bool
     */
    public function checkAccessToken(string $accessToken, string $openid): bool
    {
        $url = "https://api.weixin.qq.com/sns/auth?access_token={$accessToken}&openid={$openid}";

        $response = httpGet($url);
        $result = json_decode($response, true) ?: [];

        return isset($result['errcode']) && $result['errcode'] == 0;
    }

    /**
     * 验证微信配置
     *
     * @param array $config
     * @throws CommonException
     */
    private function validateConfig(array $config): void
    {
        if (!isset($config['appid']) || empty($config['appid'])) {
            throw new CommonException('WechatOfficialAppidRequired');
        }

        if (!isset($config['appsecret']) || empty($config['appsecret'])) {
            throw new CommonException('WechatOfficialAppsecretRequired');
        }
    }
}

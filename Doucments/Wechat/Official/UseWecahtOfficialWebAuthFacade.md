# WechatOfficialWebAuthFacade 使用说明

## 概述

`WechatOfficialWebAuthFacade` 提供了微信公众号网页授权功能，支持静默授权和用户信息授权。

## 准备工作

### 1. 配置微信参数

在使用前需要准备微信配置：

```php
$config = [
    'appid' => 'YOUR_WECHAT_OFFICIAL_APPID',      // 微信 AppID
    'appsecret' => 'YOUR_WECHAT_OFFICIAL_SECRET'   // 微信 AppSecret
];
```

### 2. 授权范围类型

| 值 | 说明 | scope值 |
|----|------|---------|
| 10 | 静默授权 | snsapi_base |
| 20 | 主动授权(可获取用户信息) | snsapi_userinfo |

## 基本使用

### 1. 获取授权URL

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\Official\WechatOfficialWebAuthFacade;

// 准备配置
$config = [
    'appid' => 'YOUR_WECHAT_OFFICIAL_APPID',
    'appsecret' => 'YOUR_WECHAT_OFFICIAL_SECRET'
];

// 获取授权URL
$authUrl = WechatOfficialWebAuthFacade::getAuthUrl(
    $config,                          // 微信配置
    20,                               // 授权类型: 10=静默, 20=主动
    'https://your-domain.com/callback',  // 回调地址
    'random_state_123'                 // 自定义状态值
);

// 跳转到微信授权页
return redirect($authUrl);
```

### 2. 处理回调并获取用户信息

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\Official\WechatOfficialWebAuthFacade;

// 准备配置
$config = [
    'appid' => 'YOUR_WECHAT_OFFICIAL_APPID',
    'appsecret' => 'YOUR_WECHAT_OFFICIAL_SECRET'
];

// 获取微信返回的code
$code = request()->input('code');

// 一键式授权流程(获取access_token和用户信息)
$result = WechatOfficialWebAuthFacade::authorize(
    $config,  // 微信配置
    $code,     // 微信授权码
    true       // 是否获取用户信息
);

// 返回数据包含:
// [
//     'access_token' => 'xxx',
//     'expires_in' => 7200,
//     'refresh_token' => 'xxx',
//     'openid' => 'xxx',
//     'scope' => 'snsapi_userinfo',
//     'userinfo' => [              // 如果 $needUserInfo = true
//         'openid' => 'xxx',
//         'nickname' => '用户昵称',
//         'sex' => 1,
//         'province' => '省份',
//         'city' => '城市',
//         'country' => '国家',
//         'headimgurl' => '头像URL',
//         'unionid' => 'xxx'
//     ]
// ]
```

## 完整示例

### 完整的授权流程

```php
namespace App\Http\Controllers;

use YouHuJun\Tool\App\Facade\V1\Wechat\Official\WechatOfficialWebAuthFacade;
use App\Models\User;

class WechatAuthController extends Controller
{
    // 步骤1: 获取授权URL
    public function auth()
    {
        // 获取微信配置
        $config = [
            'appid' => env('WECHAT_OFFICIAL_APPID'),
            'appsecret' => env('WECHAT_OFFICIAL_APPSECRET')
        ];

        // 生成授权URL
        $authUrl = WechatOfficialWebAuthFacade::getAuthUrl(
            $config,
            20,  // 主动授权
            url('/wechat/callback'),  // 回调地址
            'auth_' . time()  // 状态值
        );

        return redirect($authUrl);
    }

    // 步骤2: 处理回调
    public function callback(Request $request)
    {
        $code = $request->input('code');

        // 获取微信配置
        $config = [
            'appid' => env('WECHAT_OFFICIAL_APPID'),
            'appsecret' => env('WECHAT_OFFICIAL_APPSECRET')
        ];

        try {
            // 获取用户信息
            $result = WechatOfficialWebAuthFacade::authorize(
                $config,
                $code,
                true  // 获取用户信息
            );

            // 查找或创建用户
            $user = User::updateOrCreate(
                ['openid' => $result['openid']],
                [
                    'nickname' => $result['userinfo']['nickname'],
                    'avatar' => $result['userinfo']['headimgurl'],
                    'sex' => $result['userinfo']['sex'],
                    'province' => $result['userinfo']['province'],
                    'city' => $result['userinfo']['city']
                ]
            );

            // 登录用户
            auth()->login($user);

            return redirect('/home');

        } catch (\Exception $e) {
            return response()->json(['error' => '授权失败: ' . $e->getMessage()]);
        }
    }
}
```

### 分步骤授权流程

```php
namespace App\Services;

use YouHuJun\Tool\App\Facade\V1\Wechat\Official\WechatOfficialWebAuthFacade;

class WechatAuthService
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'appid' => env('WECHAT_OFFICIAL_APPID'),
            'appsecret' => env('WECHAT_OFFICIAL_APPSECRET')
        ];
    }

    // 获取授权URL
    public function getAuthUrl($redirectUrl)
    {
        return WechatOfficialWebAuthFacade::getAuthUrl(
            $this->config,
            20,  // 主动授权
            $redirectUrl,
            uniqid('auth_')
        );
    }

    // 仅获取access_token
    public function getAccessToken($code)
    {
        $tokenInfo = WechatOfficialWebAuthFacade::getAccessToken(
            $this->config,
            $code
        );

        return $tokenInfo;
    }

    // 获取用户信息
    public function getUserInfo($accessToken, $openid)
    {
        $userInfo = WechatOfficialWebAuthFacade::getUserInfo(
            $accessToken,
            $openid
        );

        return $userInfo;
    }

    // 刷新access_token
    public function refreshToken($refreshToken)
    {
        $newToken = WechatOfficialWebAuthFacade::refreshAccessToken(
            $this->config,
            $refreshToken
        );

        return $newToken;
    }

    // 检查token是否有效
    public function checkToken($accessToken, $openid)
    {
        return WechatOfficialWebAuthFacade::checkAccessToken(
            $accessToken,
            $openid
        );
    }
}
```

### 静默授权示例

```php
namespace App\Http\Controllers;

use YouHuJun\Tool\App\Facade\V1\Wechat\Official\WechatOfficialWebAuthFacade;

class WechatAuthController extends Controller
{
    // 静默授权(仅获取openid，不获取用户信息)
    public function silentAuth()
    {
        $config = [
            'appid' => env('WECHAT_OFFICIAL_APPID'),
            'appsecret' => env('WECHAT_OFFICIAL_APPSECRET')
        ];

        // 使用类型10进行静默授权
        $authUrl = WechatOfficialWebAuthFacade::getAuthUrl(
            $config,
            10,  // 静默授权
            url('/wechat/silent-callback'),
            'silent_' . time()
        );

        return redirect($authUrl);
    }

    public function silentCallback(Request $request)
    {
        $code = $request->input('code');
        $config = [
            'appid' => env('WECHAT_OFFICIAL_APPID'),
            'appsecret' => env('WECHAT_OFFICIAL_APPSECRET')
        ];

        // 只获取access_token，不获取用户信息
        $result = WechatOfficialWebAuthFacade::authorize(
            $config,
            $code,
            false  // 不获取用户信息
        );

        // 仅返回: access_token, expires_in, refresh_token, openid, scope
        return response()->json([
            'openid' => $result['openid'],
            'message' => '静默授权成功'
        ]);
    }
}
```

## 方法说明

| 方法 | 说明 | 参数 |
|------|------|------|
| `getAuthUrl()` | 获取授权URL | `$config`, `$scopeType`, `$authRedirectUrl`, `$state` |
| `getAccessToken()` | 获取access_token | `$config`, `$code` |
| `getUserInfo()` | 获取用户信息 | `$accessToken`, `$openid`, `$lang` |
| `authorize()` | 完整授权流程 | `$config`, `$code`, `$needUserInfo` |
| `refreshAccessToken()` | 刷新access_token | `$config`, `$refreshToken` |
| `checkAccessToken()` | 检查token是否有效 | `$accessToken`, `$openid` |

## 参数说明

### getAuthUrl()

- `$config`: 微信配置数组，必须包含 `appid` 和 `appsecret`
- `$scopeType`: 授权类型，`10`=静默授权，`20`=主动授权
- `$authRedirectUrl`: 授权后重定向的URL
- `$state`: 自定义状态值，用于防止CSRF攻击

### authorize()

- `$config`: 微信配置数组，必须包含 `appid` 和 `appsecret`
- `$code`: 微信返回的授权码
- `$needUserInfo`: 是否获取用户信息，默认为 `true`

## 返回数据格式

### authorize() 返回数据

```php
[
    'access_token' => 'xxx',           // 访问令牌
    'expires_in' => 7200,              // 过期时间(秒)
    'refresh_token' => 'xxx',          // 刷新令牌
    'openid' => 'xxx',                // 用户唯一标识
    'scope' => 'snsapi_userinfo',     // 授权范围
    'userinfo' => [                    // 用户信息(如果$needUserInfo=true)
        'openid' => 'xxx',
        'nickname' => '昵称',
        'sex' => 1,                  // 性别: 1=男, 2=女
        'province' => '省份',
        'city' => '城市',
        'country' => '国家',
        'headimgurl' => '头像URL',
        'privilege' => [],             // 用户特权
        'unionid' => 'xxx'            // 开放平台唯一标识
    ]
]
```

## 注意事项

1. **配置要求**: `appid` 和 `appsecret` 必须从微信公众平台获取
2. **授权域名**: 回调域名必须在微信公众平台配置
3. **授权类型**:
   - 静默授权(10): 仅获取 `openid`，用户无感知
   - 主动授权(20): 需要用户同意，可获取昵称、头像等信息
4. **access_token有效期**: 7200秒(2小时)
5. **refresh_token**: 可用于刷新 access_token，有效期较长
6. **state参数**: 建议使用随机值，用于防止CSRF攻击

<?php
/*
 * @Descripttion: 组件包通用错误码配置文件
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-06 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 11:16:49
 * @FilePath: \src\config\error-codes.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

/**
 * 系统错误码
 */
$systemCodeArray =  [
    // ========== 通用错误码 (10000-19999) ==========
    'ServerError' => [
        'code' => 10000,
        'error' => 'ServerError',
        'msg' => '服务器异常'
    ],
    'CodeError' => [
        'code' => 10010,
        'error' => 'CodeError',
        'msg' => '错误码不存在'
    ],
    'ParameterError' => [
        'code' => 10020,
        'error' => 'ParameterError',
        'msg' => '参数错误'
    ],
    'UnauthorizedError' => [
        'code' => 10030,
        'error' => 'UnauthorizedError',
        'msg' => '未授权访问'
    ],
    'ForbiddenError' => [
        'code' => 10040,
        'error' => 'ForbiddenError',
        'msg' => '禁止访问'
    ],
    'NotFoundError' => [
        'code' => 10050,
        'error' => 'NotFoundError',
        'msg' => '资源不存在'
    ],
    'PathFormatError' => [
        'code' => 10060,
        'error' => 'PathFormatError',
        'msg' => '路径格式错误'
    ],
    'UnsupportedTypeError' => [
        'code' => 10070,
        'error' => 'UnsupportedTypeError',
        'msg' => '不支持的类型'
    ],
    'FileExistsError' => [
        'code' => 10080,
        'error' => 'FileExistsError',
        'msg' => '文件已存在'
    ],

    // ========== 微信公众号相关错误码 (50000-50999) ==========
   
];
/**
 * excel错误码
 */
$excelCodeArray = [
	'ExcelImportError' => [
        'code' => 11010,
        'error' => 'ExcelImportError',
        'msg' => 'Excel导入失败'
    ],
	'FailedCreateDirectory' =>[
		'code' => 11020,
        'error' => 'FailedCreateDirectory',
        'msg' => '创建目录失败'
	]
];
/**
 * 日历相关错误码
 */
$calendarCodeArray = [
	'DateRangeError' => [
        'code' => 12010,
        'error' => 'DateRangeError',
        'msg' => '超出日期范围'
    ],
	'DateFormatError' => [
        'code' => 12020,
        'error' => 'DateFormatError',
        'msg' => '日期格式错误'
    ],
	'SolarDateError' => [
        'code' => 12030,
        'error' => 'SolarDateError',
        'msg' => '公历日期错误'
    ],
	'LunarDateFormatError' => [
        'code' => 12040,
        'error' => 'LunarDateFormatError',
        'msg' => '农历日期格式错误'
    ],
	'YearOutOfRange' => [
        'code' => 12050,
        'error' => 'YearOutOfRange',
        'msg' => '年份超出范围'
    ],
	'MonthOutOfRange' => [
        'code' => 12060,
        'error' => 'MonthOutOfRange',
        'msg' => '月份超出范围'
    ],
	'DayOutOfRange' => [
        'code' => 12070,
        'error' => 'DayOutOfRange',
        'msg' => '日期超出范围'
    ],
];
/**
 * 微信错误码
 */
$wechatCodeArray = [
	 'WechatOfficialConfigNotSet' => [
        'code' => 50000,
        'error' => 'WechatOfficialConfigNotSet',
        'msg' => '微信配置未设置'
    ],
    'WechatOfficialAppidRequired' => [
        'code' => 50010,
        'error' => 'WechatOfficialAppidRequired',
        'msg' => '微信 AppId 必填'
    ],
    'WechatOfficialAppsecretRequired' => [
        'code' => 50020,
        'error' => 'WechatOfficialAppsecretRequired',
        'msg' => '微信 AppSecret 必填'
    ],
    'WechatInvalidScopeType' => [
        'code' => 50030,
        'error' => 'WechatInvalidScopeType',
        'msg' => '无效的授权类型'
    ],
    'WechatOfficialGetAccessTokenError' => [
        'code' => 50040,
        'error' => 'WechatOfficialGetAccessTokenError',
        'msg' => '获取微信访问令牌失败'
    ],
    'WechatOfficialUserInfoError' => [
        'code' => 50050,
        'error' => 'WechatOfficialUserInfoError',
        'msg' => '获取微信用户信息失败'
    ],
    'WechatOfficialRefreshTokenError' => [
        'code' => 50060,
        'error' => 'WechatOfficialRefreshTokenError',
        'msg' => '刷新微信令牌失败'
    ],
    // ========== 微信支付相关错误码 (51000-51999) ==========
    'WechatMerchantMerchantIdError' => [
        'code' => 51000,
        'error' => 'WechatMerchantMerchantIdError',
        'msg' => '微信商户号未设置'
    ],
    'WechatMerchantMerchantSerialNumberError' => [
        'code' => 51010,
        'error' => 'WechatMerchantMerchantSerialNumberError',
        'msg' => '商户API证书序列号未设置'
    ],
    'WechatMerchantMerchantPrivateKeyError' => [
        'code' => 51020,
        'error' => 'WechatMerchantMerchantPrivateKeyError',
        'msg' => '商户私钥文件不存在或无法读取'
    ],
    'WechatMerchantWechatpayCertificateError' => [
        'code' => 51030,
        'error' => 'WechatMerchantWechatpayCertificateError',
        'msg' => '微信支付平台证书文件不存在或无法读取'
    ],
    'WechatOfficialAppIdError' => [
        'code' => 51040,
        'error' => 'WechatOfficialAppIdError',
        'msg' => '微信公众号AppId未设置'
    ],
    'WecahtMerchantNotifyUrlJsPayNotifyUrlError' => [
        'code' => 51050,
        'error' => 'WecahtMerchantNotifyUrlJsPayNotifyUrlError',
        'msg' => 'JSAPI支付回调通知地址未设置'
    ],
    'PrePayOrderByWechatJsError' => [
        'code' => 51060,
        'error' => 'PrePayOrderByWechatJsError',
        'msg' => '微信JSAPI下单失败'
    ],
    'WechatApiV3KKeyNotExistsError' => [
        'code' => 51070,
        'error' => 'WechatApiV3KKeyNotExistsError',
        'msg' => '微信APIv3密钥未设置'
    ],
    // ========== 微信小程序相关错误码 (52000-52999) ==========
    'WechatMiniProgramSecretRequired' => [
        'code' => 52000,
        'error' => 'WechatMiniProgramSecretRequired',
        'msg' => '微信小程序AppSecret未设置'
    ],
    'WechatMiniProgramLoginError' => [
        'code' => 52010,
        'error' => 'WechatMiniProgramLoginError',
        'msg' => '微信小程序登录失败'
    ],
];
/**
 * 地图错误码
 */
$mapCodeArray = [
	 // ========== 腾讯地图相关错误码 (60000-60999) ==========
    'TencentMapNoKeyError' => [
        'code' => 60000,
        'error' => 'TencentMapNoKeyError',
        'msg' => '腾讯地图Key未设置'
    ],
    'TencentMapApiRegionUrlError' => [
        'code' => 60010,
        'error' => 'TencentMapApiRegionUrlError',
        'msg' => '腾讯地图逆地理编码API地址未设置'
    ],
    'GetLocationRegionByH5TencentMapParamError' => [
        'code' => 60020,
        'error' => 'GetLocationRegionByH5TencentMapParamError',
        'msg' => '经纬度参数错误'
    ],
    'GetLocationRegionByH5TencentMapError' => [
        'code' => 60030,
        'error' => 'GetLocationRegionByH5TencentMapError',
        'msg' => '通过H5获取腾讯地图位置信息失败'
    ],
    'TencentMapApiGeocoderUrlError' => [
        'code' => 60040,
        'error' => 'TencentMapApiGeocoderUrlError',
        'msg' => '腾讯地图地理编码API地址未设置'
    ],
    'TencentMapGeocoderError' => [
        'code' => 60050,
        'error' => 'TencentMapGeocoderError',
        'msg' => '腾讯地图地理编码失败'
    ],
    'TencentMapApiDistanceUrlError' => [
        'code' => 60060,
        'error' => 'TencentMapApiDistanceUrlError',
        'msg' => '腾讯地图距离计算API地址未设置'
    ],
    'TencentMapCalculateDistanceError' => [
        'code' => 60070,
        'error' => 'TencentMapCalculateDistanceError',
        'msg' => '腾讯地图距离计算失败'
    ],
];

/**
 * 腾讯云短信错误码
 */
$smsCodeArray = [
    // ========== 腾讯云短信相关错误码 (70000-70999) ==========
    'TencentCloudSecretIdError' => [
        'code' => 70000,
        'error' => 'TencentCloudSecretIdError',
        'msg' => '腾讯云SecretId未设置'
    ],
    'TencentCloudSecretKeyError' => [
        'code' => 70010,
        'error' => 'TencentCloudSecretKeyError',
        'msg' => '腾讯云SecretKey未设置'
    ],
    'TencentCloudSmsApConfigError' => [
        'code' => 70020,
        'error' => 'TencentCloudSmsApConfigError',
        'msg' => '腾讯云短信地域配置未设置'
    ],
    'TencentCloudSmsSdkAppIdError' => [
        'code' => 70030,
        'error' => 'TencentCloudSmsSdkAppIdError',
        'msg' => '腾讯云短信SdkAppId未设置'
    ],
    'TencentCloudSmsSignNameError' => [
        'code' => 70040,
        'error' => 'TencentCloudSmsSignNameError',
        'msg' => '腾讯云短信签名未设置'
    ],
    'TencentCloudSmsTemplateIdError' => [
        'code' => 70050,
        'error' => 'TencentCloudSmsTemplateIdError',
        'msg' => '腾讯云短信模板ID未设置'
    ],
    'TencentCloudSmsPhonePreError' => [
        'code' => 70060,
        'error' => 'TencentCloudSmsPhonePreError',
        'msg' => '腾讯云短信手机号前缀未设置'
    ],
    'TencentCloudSmsContentError' => [
        'code' => 70070,
        'error' => 'TencentCloudSmsContentError',
        'msg' => '腾讯云短信内容参数错误'
    ],
    'TencentCloudSmsPhoneNumberError' => [
        'code' => 70080,
        'error' => 'TencentCloudSmsPhoneNumberError',
        'msg' => '腾讯云短信手机号参数错误'
    ],
    'TencentCloudSmsSendError' => [
        'code' => 70090,
        'error' => 'TencentCloudSmsSendError',
        'msg' => '腾讯云短信发送失败'
    ],
    'TencentCloudSmsError' => [
        'code' => 70100,
        'error' => 'TencentCloudSmsError',
        'msg' => '腾讯云短信服务异常'
    ],
];

/**
 * 二维码错误码
 */
$qrcodeCodeArray = [
    // ========== 二维码相关错误码 (80000-80999) ==========
    'QrcodeDataRequired' => [
        'code' => 80000,
        'error' => 'QrcodeDataRequired',
        'msg' => '二维码数据必填'
    ],
    'QrcodeSavePathRequired' => [
        'code' => 80010,
        'error' => 'QrcodeSavePathRequired',
        'msg' => '二维码保存路径必填'
    ],
    'QrcodeSaveError' => [
        'code' => 80020,
        'error' => 'QrcodeSaveError',
        'msg' => '二维码保存失败'
    ],
    'QrcodeModeError' => [
        'code' => 80030,
        'error' => 'QrcodeModeError',
        'msg' => '二维码输出模式错误'
    ],
];

/**
 * 抖音错误码
 */
$douyinCodeArray = [
    // ========== 抖音小程序相关错误码 (53000-53999) ==========
    'DouYinMiniGameSecretRequired' => [
        'code' => 53000,
        'error' => 'DouYinMiniGameSecretRequired',
        'msg' => '抖音小游戏AppSecret未设置'
    ],
    'DouYinMiniGameLoginError' => [
        'code' => 53010,
        'error' => 'DouYinMiniGameLoginError',
        'msg' => '抖音小游戏登录失败'
    ],
    'DouYinMiniProgramSecretRequired' => [
        'code' => 53020,
        'error' => 'DouYinMiniProgramSecretRequired',
        'msg' => '抖音小程序AppSecret未设置'
    ],
    'DouYinMiniProgramLoginError' => [
        'code' => 53030,
        'error' => 'DouYinMiniProgramLoginError',
        'msg' => '抖音小程序登录失败'
    ],
];

/**
 * 加密解密错误码
 */
$secretCodeArray = [
    // ========== AES加解密相关错误码 (90000-90999) ==========
    'AESMethodError' => [
        'code' => 90000,
        'error' => 'AESMethodError',
        'msg' => 'AES加密方法无效'
    ],
    'AESEncryptDataEmpty' => [
        'code' => 90010,
        'error' => 'AESEncryptDataEmpty',
        'msg' => 'AES加密数据为空'
    ],
    'AESEncryptKeyEmpty' => [
        'code' => 90020,
        'error' => 'AESEncryptKeyEmpty',
        'msg' => 'AES加密密钥为空'
    ],
    'AESEncryptFailed' => [
        'code' => 90030,
        'error' => 'AESEncryptFailed',
        'msg' => 'AES加密失败'
    ],
    'AESDecryptDataEmpty' => [
        'code' => 90040,
        'error' => 'AESDecryptDataEmpty',
        'msg' => 'AES解密数据为空'
    ],
    'AESDecryptKeyEmpty' => [
        'code' => 90050,
        'error' => 'AESDecryptKeyEmpty',
        'msg' => 'AES解密密钥为空'
    ],
    'AESDecryptDataInvalid' => [
        'code' => 90060,
        'error' => 'AESDecryptDataInvalid',
        'msg' => 'AES解密数据格式无效'
    ],
    'AESDecryptFailed' => [
        'code' => 90070,
        'error' => 'AESDecryptFailed',
        'msg' => 'AES解密失败'
    ],

    // ========== RSA加解密相关错误码 (91000-91999) ==========
    'RSAPublicKeyEmpty' => [
        'code' => 91000,
        'error' => 'RSAPublicKeyEmpty',
        'msg' => 'RSA公钥为空'
    ],
    'RSAPublicKeyFormatNotSupported' => [
        'code' => 91010,
        'error' => 'RSAPublicKeyFormatNotSupported',
        'msg' => 'RSA公钥格式不支持'
    ],
    'RSAPublicKeyBase64DecodeFailed' => [
        'code' => 91020,
        'error' => 'RSAPublicKeyBase64DecodeFailed',
        'msg' => 'RSA公钥Base64解码失败'
    ],
    'RSAPublicKeyLoadFailed' => [
        'code' => 91030,
        'error' => 'RSAPublicKeyLoadFailed',
        'msg' => 'RSA公钥加载失败'
    ],
    'RSAPrivateKeyEmpty' => [
        'code' => 91040,
        'error' => 'RSAPrivateKeyEmpty',
        'msg' => 'RSA私钥为空'
    ],
    'RSAPrivateKeyFormatNotSupported' => [
        'code' => 91050,
        'error' => 'RSAPrivateKeyFormatNotSupported',
        'msg' => 'RSA私钥格式不支持'
    ],
    'RSAPrivateKeyBase64DecodeFailed' => [
        'code' => 91060,
        'error' => 'RSAPrivateKeyBase64DecodeFailed',
        'msg' => 'RSA私钥Base64解码失败'
    ],
    'RSAPrivateKeyLoadFailed' => [
        'code' => 91070,
        'error' => 'RSAPrivateKeyLoadFailed',
        'msg' => 'RSA私钥加载失败'
    ],
    'RSAEncryptDataEmpty' => [
        'code' => 91080,
        'error' => 'RSAEncryptDataEmpty',
        'msg' => 'RSA加密数据为空'
    ],
    'RSAEncryptFailed' => [
        'code' => 91090,
        'error' => 'RSAEncryptFailed',
        'msg' => 'RSA加密失败'
    ],
    'RSAEncryptException' => [
        'code' => 91100,
        'error' => 'RSAEncryptException',
        'msg' => 'RSA加密异常'
    ],
    'RSADecryptDataEmpty' => [
        'code' => 91110,
        'error' => 'RSADecryptDataEmpty',
        'msg' => 'RSA解密数据为空'
    ],
    'RSADecryptDataInvalid' => [
        'code' => 91120,
        'error' => 'RSADecryptDataInvalid',
        'msg' => 'RSA解密数据格式无效'
    ],
    'RSADecryptFailed' => [
        'code' => 91130,
        'error' => 'RSADecryptFailed',
        'msg' => 'RSA解密失败'
    ],
    'RSADecryptException' => [
        'code' => 91140,
        'error' => 'RSADecryptException',
        'msg' => 'RSA解密异常'
    ],
];

/**
 * 七牛云存储错误码
 */
$qiNiuCodeArray = [
    // ========== 七牛云存储相关错误码 (92000-92999) ==========
    'QiNiuAccessKeyError' => [
        'code' => 92000,
        'error' => 'QiNiuAccessKeyError',
        'msg' => '七牛云AccessKey未设置或为空'
    ],
    'QiNiuSecretKeyError' => [
        'code' => 92010,
        'error' => 'QiNiuSecretKeyError',
        'msg' => '七牛云SecretKey未设置或为空'
    ],
    'QiNiuBucketError' => [
        'code' => 92020,
        'error' => 'QiNiuBucketError',
        'msg' => '七牛云存储空间名称未设置或为空'
    ],
    'QiNiuCdnUrlError' => [
        'code' => 92030,
        'error' => 'QiNiuCdnUrlError',
        'msg' => '七牛云CDN域名未设置'
    ],
    'QiNiuFileNotFoundError' => [
        'code' => 92040,
        'error' => 'QiNiuFileNotFoundError',
        'msg' => '七牛云上传文件不存在'
    ],
    'QiNiuFilePathEmpty' => [
        'code' => 92050,
        'error' => 'QiNiuFilePathEmpty',
        'msg' => '七牛云文件路径为空'
    ],
    'QiNiuUploadFileError' => [
        'code' => 92060,
        'error' => 'QiNiuUploadFileError',
        'msg' => '七牛云上传文件失败'
    ],
    'QiNiuUploadDataEmpty' => [
        'code' => 92070,
        'error' => 'QiNiuUploadDataEmpty',
        'msg' => '七牛云上传数据为空'
    ],
    'QiNiuUploadDataError' => [
        'code' => 92080,
        'error' => 'QiNiuUploadDataError',
        'msg' => '七牛云上传数据失败'
    ],
    'QiNiuDeleteFileError' => [
        'code' => 92090,
        'error' => 'QiNiuDeleteFileError',
        'msg' => '七牛云删除文件失败'
    ],
    'QiNiuGetFileInfoError' => [
        'code' => 92100,
        'error' => 'QiNiuGetFileInfoError',
        'msg' => '七牛云获取文件信息失败'
    ],
    'QiNiuNotInitialized' => [
        'code' => 92110,
        'error' => 'QiNiuNotInitialized',
        'msg' => '七牛云服务未初始化'
    ],
];

$errorCodeArray = array_merge(
    $systemCodeArray,
	$excelCodeArray,
	$calendarCodeArray,
    $wechatCodeArray,
	$mapCodeArray,
    $smsCodeArray,
    $qrcodeCodeArray,
    $douyinCodeArray,
    $secretCodeArray,
    $qiNiuCodeArray
);

 return $errorCodeArray;

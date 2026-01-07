<?php
/*
 * @Descripttion: 组件包通用错误码配置文件
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-06 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-06 00:01:25
 * @FilePath: config/error-codes.php
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
];

$errorCodeArray = array_merge(
    $systemCodeArray,
	$excelCodeArray,
	$calendarCodeArray,
    $wechatCodeArray
);

 return $errorCodeArray;

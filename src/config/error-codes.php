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
];

$errorCodeArray = array_merge(
    $systemCodeArray,
	$excelCodeArray,
    $wechatCodeArray
);

 return $errorCodeArray;

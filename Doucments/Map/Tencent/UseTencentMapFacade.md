# 腾讯地图使用文档

## 简介

`TencentMapFacade` 提供了腾讯地图相关的功能封装，包括逆地理编码、地理编码、距离计算等功能。

## 配置参数

### 腾讯地图配置数组

```php
$config = [
    'key' => 'YOUR_TENCENT_MAP_KEY',          // 腾讯地图Key
    'regionUrl' => 'https://apis.map.qq.com/ws/geocoder/v1',    // 逆地理编码API地址
    'geocoderUrl' => 'https://apis.map.qq.com/ws/geocoder/v1',  // 地理编码API地址
    'distanceUrl' => 'https://apis.map.qq.com/ws/distance/v1'   // 距离计算API地址
];
```

### 获取腾讯地图Key

1. 访问 [腾讯地图开发者平台](https://lbs.qq.com/)
2. 注册账号并登录
3. 创建应用获取 Key

## 使用方法

### 1. 通过H5获取位置信息(逆地理编码)

根据经纬度查询详细地址信息：

```php
use YouHuJun\Tool\App\Facade\V1\Map\Tencent\TencentMapFacade;

$config = [
    'key' => 'YOUR_TENCENT_MAP_KEY',
    'regionUrl' => 'https://apis.map.qq.com/ws/geocoder/v1'
];

$locationData = [
    'latitude' => '37.54061',     // 纬度
    'longitude' => '121.40011'    // 经度
];

try {
    $result = TencentMapFacade::getLocationRegionByH5($config, $locationData);

    // 返回结果
    // [
    //     'location' => [
    //         'lat' => 37.54061,
    //         'lng' => 121.40011
    //     ],
    //     'address' => '山东省烟台市芝罘区市府街63号',
    //     'formatted_addresses' => [
    //         'recommend' => '向阳烟台市芝罘区人民政府中兴楼饭庄(市府街北)',
    //         'rough' => '向阳烟台市芝罘区人民政府中兴楼饭庄(市府街北)'
    //     ],
    //     'address_component' => [
    //         'nation' => '中国',
    //         'province' => '山东省',
    //         'city' => '烟台市',
    //         'district' => '芝罘区',
    //         'street' => '市府街',
    //         'street_number' => '市府街63号'
    //     ],
    //     'ad_info' => [
    //         'nation_code' => 156,
    //         'adcode' => 370602,
    //         'phone_area_code' => '0535',
    //         'city_code' => '156370600',
    //         'name' => '中国,山东省,烟台市,芝罘区',
    //         'nation' => '中国',
    //         'province' => '山东省',
    //         'city' => '烟台市',
    //         'district' => '芝罘区'
    //     ],
    //     'address_reference' => [...]
    // ]

    // 获取地址信息
    $province = $result['address_component']['province'] ?? '';
    $city = $result['address_component']['city'] ?? '';
    $district = $result['address_component']['district'] ?? '';
    $street = $result['address_component']['street'] ?? '';
    $adcode = $result['ad_info']['adcode'] ?? '';

    echo "地址: {$province}{$city}{$district}{$street}";
    echo "行政区码: {$adcode}";

} catch (\Exception $e) {
    $error = $e->getErrorResponse();
    echo "错误码: {$error['code']}\n";
    echo "错误信息: {$error['msg']}\n";
}
```

### 2. 地理编码(地址转经纬度)

根据地址查询经纬度：

```php
$address = '山东省烟台市芝罘区市府街63号';

$config = [
    'key' => 'YOUR_TENCENT_MAP_KEY',
    'geocoderUrl' => 'https://apis.map.qq.com/ws/geocoder/v1'
];

try {
    $result = TencentMapFacade::geocoder($config, $address);

    // 返回结果
    // [
    //     'location' => [
    //         'lat' => 37.54061,
    //         'lng' => 121.40011
    //     ],
    //     'address' => '山东省烟台市芝罘区市府街63号',
    //     'ad_info' => [...]
    // ]

    $latitude = $result['location']['lat'];
    $longitude = $result['location']['lng'];

    echo "经纬度: {$latitude}, {$longitude}";

} catch (\Exception $e) {
    $error = $e->getErrorResponse();
    echo "错误码: {$error['code']}\n";
    echo "错误信息: {$error['msg']}\n";
}
```

### 3. 距离计算

计算两个坐标点之间的距离：

```php
$config = [
    'key' => 'YOUR_TENCENT_MAP_KEY',
    'distanceUrl' => 'https://apis.map.qq.com/ws/distance/v1'
];

$from = ['39.984154', '116.307490'];  // 起点(天安门)
$to = ['31.235929', '121.480539'];     // 终点(上海)
$mode = 'driving';  // 出行方式: driving(驾车)、walking(步行)、bicycling(骑行)

try {
    $result = TencentMapFacade::calculateDistance($config, $mode, $from, $to);

    // 返回结果
    // [
    //     'elements' => [
    //         [
    //             'from' => ['lat' => 39.984154, 'lng' => 116.307490],
    //             'to' => ['lat' => 31.235929, 'lng' => 121.480539],
    //             'distance' => 1258486,  // 距离(米)
    //             'duration' => 53205     // 预计时间(秒)
    //         ]
    //     ]
    // ]

    $distance = $result['elements'][0]['distance'];  // 米
    $duration = $result['elements'][0]['duration'];  // 秒

    echo "距离: " . round($distance / 1000, 2) . " 公里";
    echo "预计时间: " . round($duration / 3600, 1) . " 小时";

} catch (\Exception $e) {
    $error = $e->getErrorResponse();
    echo "错误码: {$error['code']}\n";
    echo "错误信息: {$error['msg']}\n";
}
```

### Laravel 框架中获取配置

如果你的项目使用Laravel框架：

```php
use Illuminate\Support\Facades\Cache;

$config = [
    'key' => Cache::get('tencent.map.key'),
    'regionUrl' => Cache::get('tencent.map.api.regionUrl'),
    'geocoderUrl' => Cache::get('tencent.map.api.geocoderUrl'),
    'distanceUrl' => Cache::get('tencent.map.api.distanceUrl')
];
```

## 异常处理

所有方法在失败时会抛出 `CommonException` 异常：

```php
use YouHuJun\Tool\App\Exceptions\CommonException;

try {
    $result = TencentMapFacade::getLocationRegionByH5($config, $locationData);
} catch (CommonException $e) {
    $error = $e->getErrorResponse();
    // [
    //     'code' => 60030,
    //     'error' => 'GetLocationRegionByH5TencentMapError',
    //     'msg' => '通过H5获取腾讯地图位置信息失败'
    // ]

    echo "错误码: {$error['code']}\n";
    echo "错误信息: {$error['msg']}\n";
}
```

## 错误码说明

| 错误码 | 错误标识 | 说明 |
|--------|----------|------|
| 60000 | TencentMapNoKeyError | 腾讯地图Key未设置 |
| 60010 | TencentMapApiRegionUrlError | 腾讯地图逆地理编码API地址未设置 |
| 60020 | GetLocationRegionByH5TencentMapParamError | 经纬度参数错误 |
| 60030 | GetLocationRegionByH5TencentMapError | 通过H5获取腾讯地图位置信息失败 |
| 60040 | TencentMapApiGeocoderUrlError | 腾讯地图地理编码API地址未设置 |
| 60050 | TencentMapGeocoderError | 腾讯地图地理编码失败 |
| 60060 | TencentMapApiDistanceUrlError | 腾讯地图距离计算API地址未设置 |
| 60070 | TencentMapCalculateDistanceError | 腾讯地图距离计算失败 |

## 注意事项

1. **API Key**：需要在腾讯地图开发者平台申请，每个应用都有独立的Key
2. **请求频率限制**：腾讯地图API有QPS限制，建议做好缓存
3. **坐标系统**：腾讯地图使用国测局GCJ02坐标系，如需使用其他坐标系请转换
4. **返回数据格式**：默认返回数组格式，如需对象格式可使用 `getLocationRegionByH5Object()`
5. **HTTP依赖**：使用全局的 `httpGet()` 函数发送请求

## 完整示例：获取用户位置并显示地址

```php
<?php

require_once 'vendor/autoload.php';

use YouHuJun\Tool\App\Facade\V1\Map\Tencent\TencentMapFacade;
use YouHuJun\Tool\App\Exceptions\CommonException;

// 配置
$config = [
    'key' => 'YOUR_TENCENT_MAP_KEY',
    'regionUrl' => 'https://apis.map.qq.com/ws/geocoder/v1'
];

// 假设从浏览器获取的定位
$locationData = [
    'latitude' => $_GET['lat'] ?? '37.54061',
    'longitude' => $_GET['lng'] ?? '121.40011'
];

try {
    // 逆地理编码
    $result = TencentMapFacade::getLocationRegionByH5($config, $locationData);

    // 获取地址信息
    $province = $result['address_component']['province'] ?? '';
    $city = $result['address_component']['city'] ?? '';
    $district = $result['address_component']['district'] ?? '';
    $address = $result['address'] ?? '';

    // 输出结果
    echo "详细地址: {$address}\n";
    echo "省份: {$province}\n";
    echo "城市: {$city}\n";
    echo "区县: {$district}\n";
    echo "行政区码: {$result['ad_info']['adcode']}\n";

} catch (CommonException $e) {
    $error = $e->getErrorResponse();
    echo "获取位置信息失败\n";
    echo "错误码: {$error['code']}\n";
    echo "错误信息: {$error['msg']}\n";
}
```

## 官方文档

- [腾讯地图Web服务API - 逆地址解析(坐标描述)](https://lbs.qq.com/service/webService/webServiceGuide/webServiceGuide)
- [腾讯地图Web服务API - 地址解析(经纬度查询)](https://lbs.qq.com/service/webService/webServiceGuide/webServiceGuide)
- [腾讯地图Web服务API - 距离计算](https://lbs.qq.com/service/webService/webServiceGuide/webServiceGuide)
- [腾讯地图开发者平台](https://lbs.qq.com/)

<?php
/*
 * @Descripttion: 腾讯地图服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.Map.Tencent.TencentMapFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Map\Tencent;

use YouHuJun\Tool\App\Exceptions\CommonException;

/**
 * 腾讯地图服务类
 *
 * 提供腾讯地图相关的功能,通过方法参数传递配置,去除框架耦合
 */
class TencentMapFacadeService
{
    /**
     * 通过H5获取位置信息(逆地理编码)
     *
     * @param array $config 腾讯地图配置,包含key和regionUrl
     * @param array $locationData 位置数据,包含latitude和longitude
     * @return array 返回位置信息数组
     * @throws CommonException
     */
    public function getLocationRegionByH5(array $config, array $locationData): array
    {
        // 验证参数
        if (!(isset($locationData['latitude']) && isset($locationData['longitude']))) {
            throw new CommonException('GetLocationRegionByH5TencentMapParamError');
        }

        $latitude = $locationData['latitude'];
        $longitude = $locationData['longitude'];

        // 获取腾讯地图Key
        $key = trim($config['key'] ?? '');
        if (!$key) {
            throw new CommonException('TencentMapNoKeyError');
        }

        // 获取腾讯地图逆地理编码API地址
        $regionUrl = trim($config['regionUrl'] ?? '');
        if (!$regionUrl) {
            throw new CommonException('TencentMapApiRegionUrlError');
        }

        // 构造请求URL
        $url = "{$regionUrl}?location={$latitude},{$longitude}&key={$key}";

        // 发送HTTP GET请求
        $response = httpGet($url);
        $httpGetObjectResult = json_decode($response);

        // 检查返回结果
        if (!isset($httpGetObjectResult->status) || $httpGetObjectResult->status != 0) {
            // 可以记录日志
            // error_log('腾讯地图API错误: ' . json_encode($httpGetObjectResult));
            
            throw new CommonException('GetLocationRegionByH5TencentMapError');
        }

        // 返回结果数据
        $dataResult = $httpGetObjectResult->result;

        // 转换为数组
        return (array)$dataResult;
    }

    /**
     * 通过H5获取位置信息(返回对象格式)
     *
     * @param array $config 腾讯地图配置,包含key和regionUrl
     * @param array $locationData 位置数据,包含latitude和longitude
     * @return object 返回位置信息对象
     * @throws CommonException
     */
    public function getLocationRegionByH5Object(array $config, array $locationData): object
    {
        $resultArray = $this->getLocationRegionByH5($config, $locationData);
        
        return (object)$resultArray;
    }

    /**
     * 地理编码(地址转经纬度)
     *
     * @param array $config 腾讯地图配置,包含key和geocoderUrl
     * @param string $address 要查询的地址
     * @return array 返回经纬度信息数组
     * @throws CommonException
     */
    public function geocoder(array $config, string $address): array
    {
        $key = trim($config['key'] ?? '');
        if (!$key) {
            throw new CommonException('TencentMapNoKeyError');
        }

        $geocoderUrl = trim($config['geocoderUrl'] ?? '');
        if (!$geocoderUrl) {
            throw new CommonException('TencentMapApiGeocoderUrlError');
        }

        // 构造请求URL
        $url = "{$geocoderUrl}?address=" . urlencode($address) . "&key={$key}";

        // 发送HTTP GET请求
        $response = httpGet($url);
        $result = json_decode($response);

        // 检查返回结果
        if (!isset($result->status) || $result->status != 0) {
            throw new CommonException('TencentMapGeocoderError');
        }

        return (array)$result->result;
    }

    /**
     * 距离计算
     *
     * @param array $config 腾讯地图配置,包含key和distanceUrl
     * @param string $mode 出行方式:driving(驾车)、walking(步行)、bicycling(骑行)
     * @param array $from 起点 [lat, lng]
     * @param array $to 终点 [lat, lng]
     * @return array 返回距离信息数组
     * @throws CommonException
     */
    public function calculateDistance(array $config, string $mode, array $from, array $to): array
    {
        $key = trim($config['key'] ?? '');
        if (!$key) {
            throw new CommonException('TencentMapNoKeyError');
        }

        $distanceUrl = trim($config['distanceUrl'] ?? '');
        if (!$distanceUrl) {
            throw new CommonException('TencentMapApiDistanceUrlError');
        }

        // 构造请求URL
        $fromStr = implode(',', $from);
        $toStr = implode(',', $to);
        $url = "{$distanceUrl}?mode={$mode}&from={$fromStr}&to={$toStr}&key={$key}";

        // 发送HTTP GET请求
        $response = httpGet($url);
        $result = json_decode($response);

        // 检查返回结果
        if (!isset($result->status) || $result->status != 0) {
            throw new CommonException('TencentMapCalculateDistanceError');
        }

        return (array)$result->result;
    }
}

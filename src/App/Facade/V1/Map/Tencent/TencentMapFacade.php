<?php
namespace YouHuJun\Tool\App\Facade\V1\Map\Tencent;

use YouHuJun\Tool\App\Service\V1\Map\Tencent\TencentMapFacadeService;
use BadMethodCallException;

/**
 * 腾讯地图门面类
 *
 * @see \YouHuJun\Tool\App\Service\V1\Map\Tencent\TencentMapFacadeService
 */
class TencentMapFacade
{
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于依赖注入或测试)
     *
     * @param TencentMapFacadeService $instance 服务实例
     * @return void
     */
    public static function setInstance(TencentMapFacadeService $instance): void
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
     * @return TencentMapFacadeService 服务实例
     */
    protected static function getInstance(): TencentMapFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new TencentMapFacadeService();
        }
        return static::$instance;
    }

    /**
     * 通过H5获取位置信息(逆地理编码)
     *
     * @param array $config 腾讯地图配置,包含key和regionUrl
     * @param array $locationData 位置数据,包含latitude和longitude
     * @return array 返回位置信息数组
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getLocationRegionByH5(array $config, array $locationData): array
    {
        return static::getInstance()->getLocationRegionByH5($config, $locationData);
    }

    /**
     * 通过H5获取位置信息(返回对象格式)
     *
     * @param array $config 腾讯地图配置,包含key和regionUrl
     * @param array $locationData 位置数据,包含latitude和longitude
     * @return object 返回位置信息对象
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function getLocationRegionByH5Object(array $config, array $locationData): object
    {
        return static::getInstance()->getLocationRegionByH5Object($config, $locationData);
    }

    /**
     * 地理编码(地址转经纬度)
     *
     * @param array $config 腾讯地图配置,包含key和geocoderUrl
     * @param string $address 要查询的地址
     * @return array 返回经纬度信息数组
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function geocoder(array $config, string $address): array
    {
        return static::getInstance()->geocoder($config, $address);
    }

    /**
     * 距离计算
     *
     * @param array $config 腾讯地图配置,包含key和distanceUrl
     * @param string $mode 出行方式:driving(驾车)、walking(步行)、bicycling(骑行)
     * @param array $from 起点 [lat, lng]
     * @param array $to 终点 [lat, lng]
     * @return array 返回距离信息数组
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function calculateDistance(array $config, string $mode, array $from, array $to): array
    {
        return static::getInstance()->calculateDistance($config, $mode, $from, $to);
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

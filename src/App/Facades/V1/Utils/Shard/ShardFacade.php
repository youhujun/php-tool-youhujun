<?php
namespace YouHuJun\Tool\App\Facades\V1\Utils\Shard;

use YouHuJun\Tool\App\Services\V1\Utils\Shard\ShardFacadeService;
use BadMethodCallException;

class ShardFacade
{
    /**
     * 多实例池
     */
    protected static array $instancePool = [];

    private function __construct() {}
    private function __clone() {}

    /**
     * 获取指定标识的服务实例
     * @param string $configKey 配置标识
     */
    public static function getInstance(string $configKey): ShardFacadeService
    {
        if (!isset(self::$instancePool[$configKey])) {
            self::$instancePool[$configKey] = new ShardFacadeService();
        }
        return self::$instancePool[$configKey];
    }

    /**
     * 所有静态方法强制传configKey，无默认值！
     */
    public static function calc(string|int $uid, string $configKey): array
    {
        return self::getInstance($configKey)->calc($uid, $configKey);
    }

    public static function getTableName(string|int $uid, string $baseTable, string $configKey): string
    {
        return self::getInstance($configKey)->getTableName($uid, $baseTable, $configKey);
    }

    public static function getDbName(string|int $uid, string $configKey): string
    {
        return self::getInstance($configKey)->getDbName($uid, $configKey);
    }

    public static function getShardKey(string|int $uid, string $configKey): int
    {
        return self::getInstance($configKey)->getShardKey($uid, $configKey);
    }

    /**
     * 批量设置多配置
     */
    public static function setMultiConfig(string $configKey, array $config): void
    {
        ShardFacadeService::setMultiConfig($configKey, $config);
    }

    /**
     * 获取指定标识的配置值
     */
    public static function getMultiConfig(string $configKey, string $key, mixed $default = null): mixed
    {
        return ShardFacadeService::getMultiConfig($configKey, $key, $default);
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
        // ShardFacade比较特殊,第一个参数必须是configKey
        $configKey = $parameters[0] ?? null;
        if (!$configKey) {
            throw new BadMethodCallException(
                'ShardFacade requires configKey as the first parameter'
            );
        }
        $instance = static::getInstance($configKey);
        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($instance), $method)
            );
        }
        return $instance->$method(...$parameters);
    }
}
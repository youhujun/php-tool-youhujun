<?php

namespace YouHuJun\Tool\App\Facade\V1\Excel;

use YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService;
use BadMethodCallException;

/**
 * Class ExcelFacade
 *
 * Excel 操作的门面类。
 *
 * 该类提供了一个静态接口，用于访问底层的 ExcelFacadeService 服务。
 * 它实现了单例模式以确保服务的唯一性，并通过 __callStatic 魔术方法
 * 将所有静态调用转发给服务实例，从而简化了 API 的使用。
 *
 * @package YouHuJun\Tool\App\Facade\V1\Excel
 * @method static array import(string $filePath, array $options = []) 导入Excel文件
 * @method static bool export(string $filePath, array $data, array $header = [], array $options = []) 导出数据到Excel文件
 * @method static string download(string $fileName, array $data, array $header = [], array $options = []) 直接下载生成的Excel文件
 */
class ExcelFacade
{
    /**
     * @var ExcelFacadeService|null
     */
    protected static $instance;

    /**
     * 禁止从外部实例化
     */
    private function __construct() {}

    /**
     * 禁止克隆
     */
    private function __clone() {}

    /**
     * 设置服务实例（主要用于单元测试）
     *
     * @param ExcelFacadeService $instance
     */
    public static function setInstance(ExcelFacadeService $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * 获取核心服务的单例实例
     *
     * @return ExcelFacadeService
     */
    protected static function getInstance(): ExcelFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new ExcelFacadeService();
        }

        return static::$instance;
    }

    /**
     * 魔术方法，将所有静态调用转发给服务实例
     *
     * @param string $method     被调用的方法名
     * @param array  $parameters 方法的参数数组
     *
     * @return mixed
     *
     * @throws BadMethodCallException 如果在服务实例上找不到对应的方法
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $instance = static::getInstance();

        // 检查方法是否存在，提高代码的健壮性
        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException(
                sprintf(
                    'Call to undefined method %s::%s()',
                    get_class($instance),
                    $method
                )
            );
        }

        return $instance->$method(...$parameters);
    }
}
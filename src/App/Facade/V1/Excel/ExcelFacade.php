<?php

namespace YouHuJun\Tool\App\Facade\V1\Excel;

use YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService;
use YouHuJun\Tool\App\Exceptions\CommonException;
use BadMethodCallException;

/**
 * Excel 门面类
 *
 * Excel 操作的门面类。该类提供了一个静态接口，用于访问底层的 ExcelFacadeService 服务。
 * 它实现了单例模式以确保服务的唯一性，所有方法都有完整的PHPDoc注释，支持IDE代码提示和自动补全
 *
 * @package YouHuJun\Tool\App\Facade\V1\Excel
 * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService
 */
class ExcelFacade
{
    /**
     * 单例实例
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
     * 清除单例实例(用于测试)
     *
     * @return void
     */
    public static function clearInstance(): void
    {
        static::$instance = null;
    }

    /**
     * 设置服务实例（主要用于单元测试）
     *
     * @param ExcelFacadeService $instance
     * @return void
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
     * 导出数据到Excel文件
     *
     * @param array $column 列名数组，例如 [['姓名','年龄','性别']]
     * @param array $data 数据数组，例如 [['张三',20,'男'], ['李四',25,'女']]
     * @param string $title Excel文件标题，默认为 'test'
     * @param string|null $savePath 保存路径，如果为null则直接下载，如果指定路径则保存到文件
     * @return void
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::exportExcelData()
     */
    public static function exportExcelData(array $column = [[]], array $data = [], string $title = 'test', ?string $savePath = null): void
    {
        static::getInstance()->exportExcelData($column, $data, $title, $savePath);
    }

    /**
     * 测试方法
     *
     * @return void
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::testExcel()
     */
    public static function testExcel(): void
    {
        static::getInstance()->testExcel();
    }

    /**
     * 读取Excel文件初始化
     *
     * @param string $fileUrl Excel文件路径
     * @return void
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::initReadExcel()
     */
    public static function initReadExcel(string $fileUrl): void
    {
        static::getInstance()->initReadExcel($fileUrl);
    }

    /**
     * 获取工作表的行数和列数
     *
     * @return void
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::getRowColumnNumber()
     */
    public static function getRowColumnNumber(): void
    {
        static::getInstance()->getRowColumnNumber();
    }

    /**
     * 设置当前工作表
     *
     * @param int|null $index 工作表索引，从0开始，如果为null则使用当前工作表
     * @return void
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::setWorkSheet()
     */
    public static function setWorkSheet(?int $index = null): void
    {
        static::getInstance()->setWorkSheet($index);
    }

    /**
     * 获取工作表信息
     *
     * @param string|null $key 可以是索引或工作表名称
     * @return void
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::getWorkSheet()
     */
    public static function getWorkSheet(?string $key = null): void
    {
        static::getInstance()->getWorkSheet($key);
    }

    /**
     * 获取指定行的数据
     *
     * @param int $rowIndex 行索引，从1开始
     * @return array 该行的数据数组
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::getRowData()
     */
    public static function getRowData(int $rowIndex = 1): array
    {
        return static::getInstance()->getRowData($rowIndex);
    }

    /**
     * 获取指定列的数据
     *
     * @param int $columnIndex 列索引，从1开始
     * @return array 该列的数据数组
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::getColumnData()
     */
    public static function getColumnData(int $columnIndex): array
    {
        return static::getInstance()->getColumnData($columnIndex);
    }

    /**
     * 按行获取所有数据
     *
     * @return array 二维数组，每个元素是一行的数据
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::getDataByRow()
     */
    public static function getDataByRow(): array
    {
        return static::getInstance()->getDataByRow();
    }

    /**
     * 按列获取所有数据
     *
     * @return array 二维数组，每个元素是一列的数据
     *
     * @see \YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService::getDataByColumn()
     */
    public static function getDataByColumn(): array
    {
        return static::getInstance()->getDataByColumn();
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
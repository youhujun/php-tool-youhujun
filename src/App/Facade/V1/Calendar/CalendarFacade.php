<?php
namespace YouHuJun\Tool\App\Facade\V1\Calendar;

use YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService;
use YouHuJun\Tool\App\Exceptions\CommonException;
use BadMethodCallException;

/**
 * 日历门面类
 *
 * 提供静态方法访问 CalendarFacadeService 服务
 * 所有方法都有完整的PHPDoc注释,支持IDE代码提示和自动补全
 *
 * @see \YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService
 */
class CalendarFacade
{
    /**
     * 单例实例
     * @var CalendarFacadeService|null
     */
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于测试)
     *
     * @param CalendarFacadeService $instance
     * @return void
     */
    public static function setInstance(CalendarFacadeService $instance): void
    {
        static::$instance = $instance;
    }

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
     * 获取服务实例
     *
     * @return CalendarFacadeService
     */
    protected static function getInstance(): CalendarFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new CalendarFacadeService();
        }
        return static::$instance;
    }

    /**
     * 将农历日期转换为阳历日期
     *
     * @param int $year 农历年份
     * @param int $month 农历月份
     * @param int $day 农历日期
     * @return array [年, 月, 日]
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService::lunarToSolar()
     */
    public static function lunarToSolar($year, $month, $day): array
    {
        return static::getInstance()->lunarToSolar($year, $month, $day);
    }

    /**
     * 将阳历日期转换为农历日期
     *
     * @param int $year 阳历年份
     * @param int $month 阳历月份
     * @param int $day 阳历日期
     * @return array 农历数据数组
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService::solarToLunar()
     */
    public static function solarToLunar($year, $month, $day): array
    {
        return static::getInstance()->solarToLunar($year, $month, $day);
    }

    /**
     * 将阳历日期字符串转换为农历日期字符串
     *
     * @param string $solarDateString 阳历日期字符串，格式：YYYY-MM-DD，例如 "1988-07-03"
     * @return string 农历日期字符串，格式：YYYY-MM-DD，例如 "1988-05-20"
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService::solarToLunarString()
     */
    public static function solarToLunarString(string $solarDateString): string
    {
        return static::getInstance()->solarToLunarString($solarDateString);
    }

    /**
     * 将农历日期字符串转换为阳历日期字符串
     *
     * @param string $lunarDateString 农历日期字符串，格式：YYYY-MM-DD，例如 "1988-05-20"
     * @return string 阳历日期字符串，格式：YYYY-MM-DD，例如 "1988-07-03"
     * @throws CommonException
     *
     * @see \YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService::lunarToSolarString()
     */
    public static function lunarToSolarString(string $lunarDateString): string
    {
        return static::getInstance()->lunarToSolarString($lunarDateString);
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
        $instance = static::getInstance();
        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($instance), $method)
            );
        }
        return $instance->$method(...$parameters);
    }
}
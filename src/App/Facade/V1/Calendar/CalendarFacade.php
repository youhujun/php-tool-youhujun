<?php
namespace YouHuJun\Tool\App\Facade\V1\Calendar;

use YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService;
use BadMethodCallException;

class CalendarFacade 
{
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    public static function setInstance(CalendarFacadeService $instance): void
    {
        static::$instance = $instance;
    }

	// 测试时可以调用的公共方法
    public static function clearInstance(): void
    {
        static::$instance = null;
    }

    protected static function getInstance(): CalendarFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new CalendarFacadeService();
        }
        return static::$instance;
    }

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